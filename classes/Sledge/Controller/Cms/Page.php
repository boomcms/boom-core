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
	* @var	Model_Version_Page	Object representing the current page.
	*/
	protected $page;

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
			$this->page = ORM::factory('Page', $page_id);
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
			if ($this->page->children_template_id == 0)
			{
				$grandparent = $this->page->parent();
				$default_template = ($grandparent->grandchild_template_id != 0)? $grandparent->grandchild_template_id : $this->page->version()->template_id;
			}
			else
			{
				$default_template = $this->page->children_template_id;
			}

			// Get all the templates which exist in the DB, ordered alphabetically.
			$templates = ORM::factory('Template')
				->where('visible', '=', TRUE)
				->order_by('name', 'asc')
				->find_all();

			// Show the form for selecting the parent page and template.
			$this->template = View::factory("$this->_view_directory/add", array(
				'templates'		=>	$templates,
				'page'			=>	$this->page,
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
			Sledge::log("Added a new page under " . $parent->version()->title, "Page ID: " . $page->id);

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
		if ( ! $this->auth->logged_in('delete_page', $this->page) OR $this->page->mptt->is_root())
		{
			throw new HTTP_Exception_403;
		}

		if (Request::current()->method() === Request::GET)
		{
			// Get request
			// Show a confirmation dialogue warning that child pages will become inaccessible and asking whether to delete the children.

			// Get the page's MPTT values.
			$mptt = $this->page->mptt;

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
				'page'	=>	$this->page,
			));
		}
		else
		{
			// POST request.
			// The confirmation dialogue has been displayed and the user has clicked the confirm button.
			// So delete the page.

			// Log the action.
			Sledge::log("Deleted page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Get the parent of the page which is being deleted.
			// We'll redirect to this after.
			$parent = $this->page->parent();

			// Are we deleting child pages?
			$with_children = ($this->request->post('with_children') == 1);

			// Delete the page.
			$this->page->delete($with_children);

			// Redirect to the parent page.
			$this->response->body($parent->link());
		}
	}

	/**
	 * Move a page to a different position in the tree (reparent).
	 *
	 * Performs checks before moving the page:
	 *	*	That the current user has the required permission.
	 *	*	The new parent ID is different to the current parent ID.
	 *	*	The new parent ID is not the current page.
	 *	*	The new parent page exists.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * parent_id	|	int		|	The page ID of the new parent page.
	 *
	 * @throws	HTTP_Exception_403
	 * @uses		Model_Version_Page::sort_children()
	 */
	public function action_move()
	{
		// Check that the current user can edit this page.
		if ( ! $this->auth->logged_in('edit_parent', $this->page))
		{
			throw new HTTP_Exception_403;
		}

		// Get the ID of the new parent page.
		$parent_id = $this->request->post('parent_id');

		// Check that the new parent ID is different to the current parent ID and that the new parent ID is not the ID of the current page.
		if ($parent_id != $this->page->mptt->parent_id AND $parent_id != $this->page->id)
		{
			// Try and get the new parent page from the database.
			$parent = ORM::factory('Page', $parent_id);

			// Does the new parent exist?
			if ($parent->loaded())
			{
				// Parent page exists, so reparent the page.

				// Log the action.
				Sledge::log("Moved page " . $this->page->version()->title . " (ID: " . $this->page->page_id . ") to child of " . $this->page->version()->title . "(ID: " . $this->page->page_id . ")");

				// Move the page to be the last child of the new parent.
				$this->page
					->mptt
					->move_to_last_child($parent_id);

				// Sort the parent's children according to it's child ordering policy.
				$parent->sort_children();
			}
			else
			{
				// Parent page doesn't exist so throw an exception.
				throw new Exception("Cannot find new parent with page ID $parent_id");
			}
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
		if ( ! $this->auth->logged_in('publish_page', $this->page))
		{
			// Now, throw a 403.
			throw new HTTP_Exception_403;
		}

		// Log the action.
		Sledge::log("Published page " . $this->page->version()->title . " (ID: " . $this->page->page_id . ")");

		// Update the page version to make it published.
		// Can't do this via the ORM as we don't want a new version to be created.
		DB::update('page_versions')
			->set(array(
				'published_from' => $_SERVER['REQUEST_TIME'],
			))
			->where('id', '=', $this->page->id)
			->execute();

		// Since we've just edited this version object directly we need to reload it to get the current data in the cache.
		$this->page->reload();

		// Show the page status.
		$this->template = View::factory("$this->_view_directory/status", array(
			'page' => $this->page
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
		$page = $this->page;

		// Don't reload the page after save by default.
		$reload = FALSE;

		/**
		* Are you allowed to be here?
		*/
		if ( ! $this->auth->logged_in('edit_page', $page))
		{
			throw new HTTP_Exception_403;
		}

		/**
		* Get the new settings and decode them.
		*/
		$data = json_decode($this->request->post('data'));

		$page->title = $data->title;

		/**
		* If the page title has been changed, update the primary URI.
		*/
		if ($page->changed('title'))
		{
			// Reload the page after save.
			$reload = TRUE;

			$parent = $page->parent();

			$prefix = ($parent->children_link_prefix)? $parent->children_link_prefix : $parent->primary_link();

			// Does the link already exist?
			$link = ORM::factory('Page_Link')
				->where('location', '=', $prefix . '/' . URL::title($page->title))
				->find();

			if ($link->loaded() OR $link->page_id !== $page->id)
			{
				// Link hasn't been used for this page before so create a new one.
				$link = ORM::factory('Page_Link')
					->values(array(
						'page_id'	=>	$page->id,
						'location'	=>	URL::generate($prefix, $page->title),
					));
			}

			// If this the home page then only save the new URI as a secondary uri.
			if ( ! $page->mptt->is_root())
			{
				// Page isn't the homepage so make the link primary.
				$link->is_primary = TRUE;
				$link->make_primary();
			}

			// Saved the link
			$link->save();
		}

		/**
		 * If the version hasn't been changed, force it to create a new page version.
		 * This allows us to ensure versioning of changes to page slots.
		 */
		if ( ! $page->changed())
		{
			$values = $page->object();

			$page = ORM::factory('Page')
				->values($values);

			// Don't copy the value of the published columns.
			$page->published_from = $page->published_to = NULL;
		}

		/**
		* Save the new settings.
		*/
		$page->save();

		// Log the action.
		Sledge::log("Saved page $page->title (ID: $page->id)");

		// Change the page's primary URI.
		if (isset($data->uri) AND $data->uri != $page->primary_link())
		{
			// Reload the page after save.
			$reload = TRUE;

			Request::factory('cms/page/uri/primary/' . $page->pk())->post(array('uri' => $data->uri))->execute();
		}

		// Update slots.
		foreach (array('asset', 'feature', 'linkset', 'slideshow', 'text') as $slottype)
		{
			Request::factory("cms/chunk/$slottype/save")
				->post(array(
					'chunks'	=>	(array) $data->slots->$slottype,
				))
				->execute();
		}

		Request::factory('cms/page/save_slots/' . $page->pk())-> post( array('slots' => $data->slots))->execute();

		// Are we publishing this version?
		if (isset($data->publish))
		{
			Request::factory('cms/page/publish/' . $page->pk())->execute();
		}

		$this->response->body(json_encode(
			array(
				'reload'	=>	$reload,
				'url'		=> $page->link(),
				'vid'		=> $this->page->id,
				'status'	=>	View::factory("$this->_view_directory/status", array('auth' => $this->auth, 'page' => $page))->render(),
			)
		));
	}

	/**
	 * Update the page's slots.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type	|	Description
	 * ----------|-----------|---------------
	 * slots		|	array	| 	Array of stdClass objects containing the slot data for the page.
	 *
	 */
	public function action_save_slots()
	{
		$page = $this->page;

		$slots = $this->request->post('slots');

		// Build an array of slotnames submitted.
		$slotnames = array();

		foreach ( (array) $slots as $type => $obj)
		{
			foreach (get_object_vars($obj) as $name => $slot_data)
			{
				if ($this->auth->logged_in("edit_slot_$name", $page) AND ! isset($obj->delete))
				{
					$chunk = ORM::factory('Chunk');
					$chunk->type = $type;
					$chunk->slotname = $name;

					// Add this slot to the array of slotnames.
					$slotnames[] = $name;

					switch ($type)
					{
						case 'text':
							$chunk->data->text = $slot_data;
							break;

						case 'feature':
							$chunk->data->target_page_id = $slot_data;
							break;

						case 'asset':
							$chunk->data->asset_id = $slot_data;
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

							$chunk->data->slides($slot_data->slides);
							break;

						case 'linkset':
							$chunk->data->links($slot_data->links);
							break;
					}

					if ($chunk->save())
					{
						$page->add('chunks', $chunk);
					}
				}
			}
		}

		/**
		 * The code below ensures that any slots which weren't used in the current template
		 * (and therefore weren't part of the submitted data) are copied over from the previous page version
		 * to preserve them.
		 *
		 * If this code is removed then when a page has it's template changed it will lose any slots which aren't displayed in the new template
		 */
		// Get the last version ID for this page.
		$old_vid = DB::select('page_versions.id')
			->from('page_versions')
			->where('page_versions.rid', '=', $page->id)
			->where('id', '!=', $page->version->id)
			->order_by('id', 'desc')
			->limit(1)
			->execute()
			->as_array();

		if ( ! empty($old_vid) AND $old_vid !== $page->version->id)
		{
			$old_vid = $old_vid[0]['id'];

			// Copy missed slots from previous version.
			$subquery = DB::select(DB::expr($page->version->id), 'chunk_id')
				->from('page_chunk')
				->join('chunk', 'inner')
				->on('page_chunks.chunk_id', '=', 'chunks.id')
				->group_by('slotname')
				->where('page_chunks.page_vid', '=', $old_vid);

			if ( ! empty($slotnames))
			{
				$subquery->where('slotname', 'not in', $slotnames);
			}

			try
			{
				DB::insert('page_chunk', array('page_versions.id', 'chunk_id'))
					->select($subquery)
					->execute();
			}
			catch (Exception $e) {}
		}
	}

	/**
	* WYSIWYG toolbar.
	*
	*/
	public function action_text_toolbar()
	{

			$v = View::factory('sledge/editor/page/text_toolbar');

			$this->response->body($v);
	}
} // End Sledge_Controller_Cms_Page
