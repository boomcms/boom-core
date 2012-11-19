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
	protected $page;

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

		$page_id = $this->request->param('id');

		if ($page_id)
		{
			$this->page = ORM::factory('Page', $page_id);

			// Most of these methods can be  sent a version ID.
			// This allows viewing an old version and then editing / publishing that version.
			// Most of the time the vid will be page's current version ID - i.e. the user is viewing page as standard.
			// So check the vid, and load an older version if necessary.
			if (is_string($this->request->post('data')))
			{
				$data = json_decode($this->request->post('data'));
				if (isset($data->vid))
				{
					$vid = $data->vid;
				}
			}
			elseif ($this->request->query('vid'))
			{
				$vid = $this->request->query('vid');
			}

			if (isset($vid) AND $this->page->active_vid !== $vid)
			{
				$this->page->version = ORM::factory('page_version', $vid);
			}
		}
	}

	/**
	 * Returns a json encoded array of the people currently viewing the same page is this user.
	 * This is queried periodically with an AJAX request to tell the user when other people are viewing the same page.
	 * The last_active time of the current user is also updated.
	 *
	 * When making the request the javascript sends the ID of the page that is open in the browser window.
	 * This controller SHOULD return an array of people viewing that page.
	 * The controller SHOULD check that the current user is recorded as viewing the page specificed. This prevents people typing things into their browser (or making scripts) to get information for which they have no need.
	 * When the user is not recorded as viewing the specified page the controller SHOULD return a json encoded empty array.
	 *
	 * @return 	string	A json encoded array
	 */
	public function action_active_people()
	{
		// Get the current time
		$time = time();

		// Update the time that we were last active on this page.
		// We use to ensure that people who closed their browser and wandered off don't appear as editing the page.
		$us = ORM::factory('Person_Page', array(
			'person_id' => $this->person->id,
			'page_id'	=> $this->page->id,
		));

		// Are they known to be viewing this page?
		if ($us->loaded())
		{
			$us->last_active = $time;
			$us->save();

			// Get the details of the other people viewing this page who were last active in the past 2 minutes
			// The JS polls the server every 30 seconds at the moment so two moments allows for a couple of failed requests.
			// We're interesed in their person ID, when they started looking at the page, and whether they've saved the page.
			$details = DB::select('people_pages.person_id', 'people_pages.since', 'people_pages.saved', 'people.firstname', 'people.lastname')
				->from('people_pages')
				->join('people', 'inner')
				->on('people_pages.person_id', '=', 'people.id')
				->where('people_pages.page_id', '=', $us->page_id)
				->where('people_pages.person_id', '!=', $this->person->id)
				->where('people_pages.last_active', '>=', $time - 120)
				->execute();

			$people = array();

			foreach ($details as $detail)
			{
				$people[] = array(
					'id'			=>	$detail['person_id'],
					'name'		=>	$detail['firstname'] . " " . $detail['lastname'],
					'since'		=>	$detail['since'],
					'saved'		=>	(bool) $detail['saved'],
				);
			}

			// Send it back to the JS
			$response = $people;
		}
		else
		{
			$response = array();
		}

		$this->response
			->headers('content-type', 'application/json')
			->body(json_encode($response));
	}

	/**
	 * Add a new page to the CMS.
	 * If not parent ID and template ID are set a template allowing the user to set these values is displayed.
	 *
	 * **Accepted POST variables:**
	 * Name			|	Type		|	Description
	 * ---------------------|-----------------|---------------
	 * parent_id		|	int		|	The ID of the page our new page should be created as a child of.
	 * template_id		|	int		|	ID of the template to be used by the new page.
	 *
	 * @uses URL::generate()
	 */
	public function action_add()
	{
		if ($this->request->post('parent_id') !== NULL AND $this->request->post('template_id') !== NULL)
		{
			// Find the parent page.
			$parent = ORM::factory('Page', $this->request->post('parent_id'));

			// Check for add permissions on the parent page.
			if ( ! $this->auth->logged_in('add_page', $parent))
			{
				throw new HTTP_Exception_403;
			}

			// Which template to use?
			$template = $this->request->post('template_id');
			if ( ! $template)
			{
				// Inherit from parent.
				$template = ($parent->default_child_template_id != 0)? $parent->default_child_template_id : $parent->template_id;
			}

			// Create a new page object.
			$page = ORM::factory('Page');
			$page->visible = FALSE;
			$page->title = 'Untitled';
			$page->visible_in_leftnav = $parent->children_visible_in_leftnav;
			$page->visible_in_leftnav_cms = $parent->children_visible_in_leftnav_cms;
			$page->children_visible_in_leftnav = $parent->children_visible_in_leftnav;
			$page->children_visible_in_leftnav_cms = $parent->children_visible_in_leftnav_cms;
			$page->visible_from = time();
			$page->template_id = $template;
			$page->save();

			// Add to the tree.
			$page->mptt->id = $page->id;

			// Where should we put it?
			$parent->add_child($page);

			// Save the page.
			$page->save();

			// URI needs to be generated after the MPTT is set up.
			$prefix = ($parent->default_child_uri_prefix)? $parent->default_child_uri_prefix : $parent->primary_link();
			$uri = URL::generate($prefix, $page->title);

			// Add the URI as the primary URI for this page.
			Request::factory('cms/page/uri/primary/' . $page->pk())->post(array('uri' => $uri))->execute();

			// Logging.
			Sledge::log("Added a new page under " . $parent->title, "Page ID: " . $page->id);

			$this->response->body(URL::site($uri));
		}
		else
		{
			// Work out what the default template should be.
			// Priority is the parent page's default_child_template_id, then the grandparent's default_grandchild_template_id, then the parent page template id.
			if ($this->page->default_child_template_id == 0)
			{
				$grandparent = $this->page->parent();
				$default_template = ($grandparent->default_grandchild_template_id != 0)? $grandparent->default_grandchild_template_id : $this->page->template_id;
			}
			else
			{
				$default_template = $this->page->default_child_template_id;
			}

			$this->template = View::factory('sledge/editor/page/add', array(
				'templates'		=>	ORM::factory('Template')
					->where('visible', '=', TRUE)
					->order_by('name', 'asc')
					->find_all(),
				'page'			=>	$this->page,
				'default_template'	=>	$default_template,
			));
		}
	}

	/**
	 * Delete page controller.
	 * This is a dual function controller. If requested via GET a confirmation dialogue is displayed.
	 * If requested via POST the page is deleted using Model_Page::delete().
	 *
	 * @uses	Model_Page::delete()
	 */
	public function action_delete()
	{
		if ( ! $this->auth->logged_in('delete_page', $this->page) OR $this->page->mptt->is_root())
		{
			throw new HTTP_Exception_403;
		}

		if (Request::current()->method() === Request::POST)
		{
			Sledge::log("Deleted page " . $this->page->title . " (ID: " . $this->page->id . ")");

			// Get the parent page. We'll redirect to this after.
			$parent = $this->page->mptt->parent_id;
			$parent = ORM::factory('Page', $parent);

			// Are we deleting child pages?
			$delete_children = ($this->request->post('with_children') == 1);

			// Delete the page.
			$this->page->delete($delete_children);

			// Redirect to the parent page.
			$this->response->body($parent->link());
		}
		else
		{
			$mptt = $this->page->mptt;

			/**
			* Get the titles of this page's descendant pages to tell the user that these pages will disappear.
			*/
			$titles = array();

			if ($mptt->has_children())
			{
				$titles = DB::select('page_versions.title')
					->from('pages')
					->join('page_versions', 'inner')
					->on("page." . Page::join_column($this->page, $this->auth), '=', 'page_versions.id')
					->join('page_mptt', 'inner')
					->on('page_mptt.id', '=', 'pages.id')
					->where('scope', '=', $mptt->scope)
					->where('lft', '>', $mptt->lft)
					->where('rgt', '<', $mptt->rgt)
					->order_by('title', 'asc')
					->execute()
					->as_array('title');
			}

			$this->template = View::factory('sledge/editor/page/delete', array(
				'count'	=>	count($titles),
				'titles'	=>	$titles,
				'page'	=>	$this->page,
			));
		}
	}

	/**
	 * Move a page to a different position in the tree (reparent).
	 * Performs a permissions check and ensures that:
	 * - The new parent ID is not the current page.
	 * - The new parent page exists.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * parent_id	|	int		|	The page ID of the new parent page.
	 *
	 * @throws	Exception
	 * @throws	HTTP_Exception_403
	 * @uses		Model_Page::sort_children()
	 */
	public function action_move()
	{
		if ( ! $this->auth->logged_in('edit_parent', $this->page))
		{
			throw new HTTP_Exception_403;
		}

		$parent_id = $this->request->post('parent_id');

		if ( (int) $parent_id != $this->page->mptt->parent_id AND $parent_id != $this->page->id)
		{
			$parent = ORM::factory('Page', $parent_id);

			if ($parent->loaded())
			{
				$this->page->mptt->move_to_last_child($parent_id);
				$parent->sort_children();

				Sledge::log("Moved page " . $this->page->title . " (ID: " . $this->page->id . ") to child of " . $this->page->title . "(ID: " . $this->page->id . ")");
			}
			else
			{
				throw new Exception("Cannot find new parent with page ID $parent_id");
			}
		}
	}

	/**
	 * Update the page's published version.
	 * Performs a permissions check to check that the user can perform a page publish.
	 * With no version ID the current version is made the published version.
	 * Or a version ID can be sent via $_GET['vid'] to make that version publishd.
	 */
	public function action_publish()
	{
		if ($this->auth->logged_in('publish_page', $this->page))
		{
			DB::update('pages')
				->set(array('published_vid' => $this->page->version->id))
				->where('id', '=', $this->page->id)
				->execute();

			// Set the page version's published column to TRUE.
			// Can't do this via the ORM as we don't want a new version to be created.
			DB::update('page_versions')
				->set(array('published' => TRUE))
				->where('id', '=', $this->page->version->id)
				->execute();

			// Since we've just editied this version object directly we need to reload it to get the current data in the cache.
			$this->page->reload();

			Sledge::log("Published page " . $this->page->title . " (ID: " . $this->page->id . ")");

			$this->template = View::factory('sledge/editor/page/status', array('page' => $this->page));
		}
	}

	/**
	 * Display a list of page versions.
	 *
	 */
	public function action_revisions()
	{
		if (Kohana::$environment == Kohana::PRODUCTION OR Kohana::$environment == Kohana::STAGING)
		{
			HTTP::check_cache($this->request, $this->response, $this->page->active_vid . "-" . $this->page->published_vid);
		}

		// Get all revisions back to the last published version, then all published versions before that.
		$revisions = DB::select('page_versions.id')
			->from('page_versions')
			->where('rid', '=', $this->page->id)
			->and_where_open()
			->where('id', '>=', $this->page->published_vid)
			->or_where('published', '=', TRUE)
			->and_where_close()
			->execute()
			->as_array();

		// Turn the version IDs into ORM objects.
		foreach ($revisions as & $revision)
		{
			$revision = ORM::factory('page_version', $revision['id']);
		}

		$count = count($revisions);

		$this->template = View::factory('sledge/editor/page/revisions', array(
			'count'	=>	$count,
			'revisions'	=>	$revisions,
			'page'	=>	$this->page,
		));
	}

	public function action_topbar()
	{
		// Log the current user as editing this page.
		// Get the time, we insert this into the since and last active columns so we don't want to call time() twice.
		$time = time();

		// Try and find existing details from db / cache.
		$person_page = ORM::factory('Person_Page', array(
			'person_id'	=>	$this->person->id,
			'page_id'		=>	$this->page->id
		));

		// Set the values and save.
		// By also setting the person ID we ensure that if $person_page wasn't loaded it will be created.
		// If it was loaded it will be updated.
		$person_page->values(array(
			'person_id'		=>	$this->person->id,
			'page_id'			=>	$this->page->id,
			'since'			=>	$time,
			'last_active'		=>	$time,
			'saved'			=>	FALSE,
		));
		$person_page->save();

		// Show the editor topbar
		$this->template = View::factory('sledge/editor/iframe');
		View::bind_global('config', $this->config);
		View::bind_global('page', $this->page);
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

		$page->version->title = $data->title;

		/**
		* If the page title has been changed, update the primary URI.
		*/
		if ($page->version->changed('title'))
		{
			// Reload the page after save.
			$reload = TRUE;

			$parent = $page->parent();

			$prefix = ($parent->default_child_uri_prefix)? $parent->default_child_uri_prefix : $parent->primary_link();

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
		if ( ! $page->version->changed())
		{
			$page->version = $page->version->copy();

			// Don't copy the value of the published column.
			$page->version->published = FALSE;
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
				'url' => $page->link(),
				'vid' => $this->page->version->id,
				'status'	=>	View::factory('sledge/editor/page/status', array('auth' => $this->auth, 'page' => $page))->render(),
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
						$page->version->add('chunks', $chunk);
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
	* Display a page settings template.
	*/
	public function action_settings()
	{
		$this->template = View::factory('cms/ui/site/page/settings/' . $this->request->param('tab'), array(
			'page'	=>	$this->page
		));
	}
} // End Sledge_Controller_Cms_Page
