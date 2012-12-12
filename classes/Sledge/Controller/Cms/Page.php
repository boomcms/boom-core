<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * CMS Page controller
 * Contains methods for adding / saving a page etc.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Page extends Sledge_Controller
{
	/**
	*
	* @var	Model_Page	Object representing the current page.
	*/
	protected $_page;

	/**
	 * The directory where views used by this class are stored.
	 *
	 * @var	string
	 */
	protected $_view_directory = 'sledge/editor/page';

	/**
	 * Load the current page.
	 * All of these methods should be called with a page ID in the params
	 * Before the methods are called we find the page so it can be used, clever eh?
	 *
	 * @return void
	 */
	public function before()
	{
		parent::before();

		// Has a page ID been given?
		if ($page_id = $this->request->param('id'))
		{
			// Yes! Load the page from the database.
			$this->_page = ORM::factory('Page', $page_id);
		}
	}

	/**
	 * Add a new page to the CMS.
	 * If no parent ID and template ID are set a template allowing the user to set these values is displayed.
	 *
	 * **Accepted POST variables:**
	 * Name			|	Type		|	Description
	 * ---------------------|-----------------|---------------
	 * parent_id		|	int		|	The ID of the page our new page should be created as a child of.
	 * template_id		|	int		|	ID of the template to be used by the new page.
	 *
	 * @uses	URL::generate()
	 * @uses	Model_Page::add_child()
	 */
	public function action_add()
	{
		// If no parent page ID or template ID has been given show a form to selected the parent page and template.
		if ($this->request->post('parent_id') == NULL OR $this->request->post('template_id') == NULL)
		{
			// Work out which template in the select box should be selected.
			// Priority is the parent page's children_template_id
			// then the grandparent's grandchild_template_id
			// then the parent page template id.
			if ($this->_page->children_template_id == 0)
			{
				$grandparent = $this->_page->parent();
				$default_template = ($grandparent->grandchild_template_id != 0)? $grandparent->grandchild_template_id : $this->_page->version()->template_id;
			}
			else
			{
				$default_template = $this->_page->children_template_id;
			}

			// Get all the templates which exist in the DB, ordered alphabetically.
			$templates = DB::select('id', 'name')
				->from('templates')
				->where('visible', '=', TRUE)
				->order_by('name', 'asc')
				->execute()
				->as_array('id', 'name');

			// Show the form for selecting the parent page and template.
			$this->template = View::factory("$this->_view_directory/add", array(
				'templates'		=>	$templates,
				'page'			=>	$this->_page,
				'default_template'	=>	$default_template,
			));
		}
		else
		{
			// Find the parent page.
			$parent = ORM::factory('Page',  $this->request->post('parent_id'));

			// Check for add permissions on the parent page.
			if ( ! $this->auth->logged_in('add_page', $parent))
			{
				throw new HTTP_Exception_403;
			}

			// Create the new page with nav values inherited from the parent.
			$page = ORM::factory('Page')
				->values(array(
					'visible_in_nav'				=>	$parent->children_visible_in_nav,
					'visible_in_nav_cms'			=>	$parent->children_visible_in_nav_cms,
					'children_visible_in_nav'		=>	$parent->children_visible_in_nav,
					'children_visible_in_nav_cms'	=>	$parent->children_visible_in_nav_cms
				))
				->create();

			// What the title of the page will be.
			$title = 'Untitled';

			// Create a version for the page.
			ORM::factory('Page_Version')
				->values(array(
					'edited_by'	=>	$this->person->id,
					'edited_time'	=>	time(),
					'page_id'		=>	$page->id,
					'template_id'	=>	$this->request->post('template_id'),
					'title'			=>	$title,
				))
				->create();

			// Set the ID for the page's record in the mptt table to the page_id
			$page->mptt->id = $page->id;

			// Add the page to the correct place in the tree according the parent's child ordering policy.
			$parent->add_child($page);

			// Generate the link for the page.
			// What is the prefix for the link? If a default default_chinl_link_prefix has been set for the parent then use that, otherwise use the parent's primary link.
			$prefix = ($parent->children_link_prefix)? $parent->children_link_prefix : $parent->primary_link();

			// Generate a link from the prefix and the page's title.
			$link = URL::generate($prefix, $title);

			// Add the link as the primary link for this page.
			ORM::factory('Page_Link')
				->values(array(
					'location'		=>	$link,
					'page_id'		=>	$page->id,
					'is_primary'	=>	TRUE,
				))
				->create();

			// Log the action.
			$this->_log("Added a new page under " . $parent->version()->title, "Page ID: " . $page->id);

			// Redirect the user to the new page.
			$this->response->body(URL::site($link));
		}
	}

	/**
	 * Delete page controller.
	 * This is a dual function controller. If requested via GET a confirmation dialogue is displayed.
	 * If requested via POST the page is deleted using Model_Page::delete().
	 *
	 * @uses	Model_Version_Page::delete()
	 * @uses	Model_Version_Page::parent();
	 */
	public function action_delete()
	{
		if ( ! $this->auth->logged_in('delete_page', $this->_page) OR $this->_page->mptt->is_root())
		{
			throw new HTTP_Exception_403;
		}

		if ($this->request->method() === Request::GET)
		{
			// Get request
			// Show a confirmation dialogue warning that child pages will become inaccessible and asking whether to delete the children.

			// Get the page's MPTT values.
			$mptt = $this->_page->mptt;

			// Prepare an array for the descendent page titles.
			$titles = array();

			// Does the page have children?
			if ($mptt->has_children())
			{
				// Yes, the page has children, so get the titles of the descendent pages from the database.
				$titles = DB::select('page_versions.title')
					->from('page_versions')
					->join(array(
						DB::select(array(DB::expr('max(id)'), 'id'), 'page_id')
							->from('page_versions')
							->group_by('page_id'),
						'pages'
					))
					->on('page_versions.page_id', '=', 'pages.page_id')
					->on('page_versions.id', '=', 'pages.id')
					->join('page_mptt', 'inner')
					->on('page_mptt.id', '=', 'page_versions.page_id')
					->where('scope', '=', $mptt->scope)
					->where('lft', '>', $mptt->lft)
					->where('rgt', '<', $mptt->rgt)
					->order_by('title', 'asc')
					->execute()
					->as_array();

				// Turn the results array into an array containing just the page titles.
				$titles = Arr::pluck($titles, 'title');
			}

			// Show the confirmation
			$this->template = View::factory("$this->_view_directory/delete", array(
				'count'	=>	count($titles),
				'titles'	=>	$titles,
				'page'	=>	$this->_page,
			));
		}
		else
		{
			// POST request.
			// The confirmation dialogue has been displayed and the user has clicked the confirm button.
			// So delete the page.

			// Log the action.
			$this->_log("Deleted page " . $this->_page->version()->title . " (ID: " . $this->_page->id . ")");

			// Get the parent of the page which is being deleted.
			// We'll redirect to this after.
			$parent = $this->_page->parent();

			// Are we deleting child pages?
			$with_children = ($this->request->post('with_children') == 1);

			// Delete the page.
			$this->_page->delete($with_children);

			// Redirect to the parent page.
			$this->response->body($parent->link());
		}
	}

	/**
	 * Mark a particular page version as published.
	 * Performs a permissions check to check that the user can perform a page publish.
	 * With no version ID the current version is published.
	 * Or a version ID can be sent via $_GET['vid'] to make that version publishd.
	 */
	public function action_publish()
	{
		// Does the current user have the publish_page role for this page?
		if ( ! $this->auth->logged_in('publish_page', $this->_page))
		{
			// Now, throw a 403.
			throw new HTTP_Exception_403;
		}

		// Log the action.
		$this->_log("Published page " . $this->_page->version()->title . " (ID: " . $this->_page->page_id . ")");

		// Update the page version to make it published.
		// Can't do this via the ORM as we don't want a new version to be created.
		DB::update('page_versions')
			->set(array(
				'published_from' => $_SERVER['REQUEST_TIME'],
			))
			->where('id', '=', $this->_page->id)
			->execute();

		// Since we've just edited this version object directly we need to reload it to get the current data in the cache.
		$this->_page->reload();

		// Show the page status.
		$this->template = View::factory("$this->_view_directory/status", array(
			'page' => $this->_page
		));
	}

	public function action_tree()
	{
		$pages = DB::select( array('pages.id', 'page_id'), 'v.children_ordering_policy', 'pages.visible', 'v.visible_in_nav', 'page_links.location', 'v.title', 'page_mptt.*')
			->from('pages')
			->join('page_mptt')
			->on('page_mptt.id', '=', 'pages.id')
			->join('page_links', 'inner')
			->on('page_links.page_id', '=', 'pages.id')
			->where('is_primary', '=', TRUE)
			->where('v.deleted', '=', FALSE)
			->join( array('page_versions', 'v'), 'inner')
			->on('pages.active_vid', '=', 'v.id')
			->order_by('page_mptt.lft', 'asc')
			->execute()
			->as_array();

		$this->template = View::factory('sledge/pages/tree', array(
			'pages'	=>	$pages,
			'page'	=>	ORM::factory('Page', $this->request->param('id')),
			'state'	=>	'collapsed',
		));
	}

	/**
	 * Save the page.
	 * A lot of the page content (e.g. slots, tags, primary uri) is saved via internal requests.
	 * This allows us to use those controllers externally to save that content and avoid code duplication.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type	|	Description
	 * ---------------|-----------|---------------
	 * data		|	json	|	All the page settings, slot data, tags etc. are sent via a single json encoded variable...
	 *
	 */
	public function action_save()
	{
		// Don't reload the page after save by default.
		$reload = FALSE;

		// Are you allowed to be here?
		$this->_authorization('edit_page', $this->_page);

		// Save page form data is json encoded so get and the data and decode it.
		$post = json_decode($this->request->post('data'));

		// Get the current version of the page.
		$current_version = $this->_page->version();

		// Create a new version of the page.
		$new_version = $this->_page->create_version();

		// Has the page title been changed?
		if ($current_version->title != $post->title)
		{
			// Update the title of the new version.
			$new_version->title = $post->title;

			// TODO: update the primary URL of the page the first time a title is saved.
		}

		// Save the new version.
		$new_version->save();

		// Update slots.

		// Used to build an array of slotnames submitted.
		$slotnames = array();

		foreach ( (array) $post->slots as $type => $obj)
		{
			foreach (get_object_vars($obj) as $name => $slot_data)
			{
				if ( ! isset($obj->delete))
				{
					$chunk = ORM::factory('Chunk_'.ucfirst($type));
					$chunk->slotname = $name;

					// Add this slot to the array of slotnames.
					$slotnames[] = $name;

					switch ($type)
					{
						case 'text':
							$chunk->text = $slot_data->text;
							$chunk->title = $slot_data->title;
							break;

						case 'feature':
							$chunk->target_page_id = $slot_data;
							break;

						case 'asset':
							$chunk->asset_id = $slot_data;
							break;

						case 'slideshow':
							foreach ($slot_data->slides as & $slide)
							{
								$caption = $slide->caption;
								$url = $slide->link;
								$asset_id = $slide->asset_rid;
								$slide = ORM::factory('chunk_slideshow_slide');
								$slide->asset_id = $asset_id;
								$slide->caption = $caption;

								if ($url != '#')
								{
									$slide->url = $url;
								}
							}

							$chunk->slides($slot_data->slides);
							break;

						case 'linkset':
							$chunk->links($slot_data->links);
							break;
					}

					if ($chunk->save())
					{
						$new_version->add('chunks', $chunk);
					}
				}
			}
		}

		// Import any chunks which weren't saved from the old version to the new version.
		$new_version->import_chunks($current_version, $slotnames);

		// Set the response.
		$this->response
			->body(json_encode(
				array(
					'reload'	=>	$reload,
					'url'		=>	$this->_page->link(),
					'vid'		=>	$new_version->id,
				)
			));
	}

	/**
	* WYSIWYG toolbar.
	*
	*/
	public function action_text_toolbar()
	{

		$v = View::factory(
 			'sledge/editor/page/text_toolbar',
 				array( 'mode' => $this->request->query('mode') )
 		);

		$this->response->body($v);
	}

	/**
	* slideshow toolbar.
	*
	*/
	public function action_slide_toolbar()
	{

			$v = View::factory( 'sledge/editor/page/slide_toolbar' );

			$this->response->body($v);
	}
} // End Sledge_Controller_Cms_Page