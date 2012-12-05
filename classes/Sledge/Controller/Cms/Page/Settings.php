<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ##Controller to edit page settings.
 *
 * Each function relates to a view in the sledge/editor/page/settings directory.
 *
 * When called as a GET the functions will display the relevant view.
 * When called as a POST request the controller will save the data in that view.
 *
 * The controllers are tied quite strictly to the view so that the controller will only try and save what it expects to be in the view.
 * Therefore when adding a new setting it will need to be added to the view and to the controller.
 * This is done so that the controllers can quickly save their settings without having to check what data has been sent to determine what they need to do.
 * It's more secure as well.
 *
 * This class extends the Controller_Cms_Page class so that it inherits the $_page property and the before() function.
 *
 * @package	Sledge
 * @category	Controllers
 */
class Sledge_Controller_Cms_Page_Settings extends Controller_Cms_Page
{
	/**
	 * Holds the parent page.
	 * Used for inheriting page settings from the parent.
	 *
	 * @var	Model_Page
	 */
	protected $_parent;

	/**
	 * **The request method of the current request.**
	 *
	 * The functions in this class do different things depending on whether the request is GET or POST.
	 * GET requests show a form to edit settings, POST requests update the settings.
	 *
	 * This is easy enough to get from [Request::method()] but the functions in this class use:
	 *
	 * 	if (request method is get)
	 *	elseif (request method is post)
	 *	endif
	 *
	 * So making the request method available in a class property avoid us calling [Request::method()] twice in each function.
	 *
	 * The value of this is set in [Sledge_Controller_Cms_Page_Settings::before()]
	 *
	 * @var	integer
	 */
	protected $_method;

	/**
	 *
	 * @var	string	Directory where views used by this class are stored.
	 */
	protected $_view_directory = 'sledge/editor/page/settings';

	public function before()
	{
		parent::before();

		// Assign the request method to $this->_method
		$this->_method = $this->request->method();
	}

	/**
	 * **Edit the page admin settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Internal name
	 *
	 * @uses	Sledge_Controller::_authorization()
	 * @uses	Sledge_Controller::_log()
	 */
	public function action_admin()
	{
		// Permissions check
		$this->_authorization('edit_page_admin', $this->page);

		if ($this->_method === Request::GET)
		{
			// GET request - display the admin settings form.
			$this->template = View::factory("$this->_view_directory/admin");
		}
		elseif ($this->_method == Request::POST)
		{
			// Log the action.
			$this->_log("Saved admin settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Set the new page internal name.
			$this->update_columns(array(
				'internal_name'
			));
			$this->page->save();
		}
	}

	/**
	 * **Edit the child page settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *    * Default child template
	 *    * Child ordering policy
	 *  * Advanced:
	 *    * Children visible in nav
	 *    * Children visible in CMS nav
	 *    * Default child URL prefix
	 *    * Default grandchild template
	 *
	 * @uses	Sledge_Controller::_authorization()
	 * @uses	Sledge_Controller::_log()
	 */
	public function action_children()
	{
		// Permissions check
		// These settings are divided into basic and advanced.
		// We only need to check for the basic permissions here
		// If they can't edit the basic stuff then they shouldn't have the advanced settings either.
		$this->_authorization('edit_page_children_basic', $this->page);

		if ($this->_method === REQUEST::GET)
		{
			// Show the child page settings form.

			$default_child_template = ($this->page->children_template_id != 0)? $this->page->children_template_id : $this->page->version()->template_id;
			$default_grandchild_template = ($this->page->grandchild_template_id != 0)? $this->page->grandchild_template_id : $this->page->version()->template_id;

			$this->template = View::factory("$this->_view_directory/children", array(
				'default_child_template'		=>	$default_child_template,
				'default_grandchild_template'	=>	$default_grandchild_template,
				'page'					=>	$this->page,
			));
		}
		elseif ($this->_method === Request::POST)
		{
			// Save the new settings.

			$this->update_inherited_columns(array(
				'children_visible_in_nav', 'children_visible_in_nav_cms'
			));

			$this->page->children_ordering_policy($this->request->post('children_ordering_policy'), $this->request->post('child_ordering_direction'));

			// These settings aren't inherited
			$this->update_columns(array(
				'grandchild_template_id', 'children_template_id', 'children_link_prefix'
			));

			$this->page->save();

			// Updated existing child pages with leftnav visibility or template.
			if ($this->request->post('visible_in_nav_cascade') == 1 OR
				$this->request->post('visible_in_nav_cms_cascase') == 1 OR
				$this->request->post('child_template_cascade') == 1
			)
			{
				$child_template = ($this->page->children_template_id == 0)? $this->page->version()->template_id : $this->page->children_template_id;

				$children = DB::select('page_mptt.id')
					->from('page_mptt')
					->where('scope', '=', $this->page->mptt->scope)
					->where('lft', '>', $this->page->mptt->lft)
					->where('rgt', '<', $this->page->mptt->rgt)
					->execute();

				foreach ($children as $child)
				{
					$child = ORM::factory('Page', $child['id']);

					if ($this->request->post('visible_in_nav_cascade') == 1)
					{
						$child->visible_in_nav = $this->page->children_visible_in_nav;
					}
					if ($this->request->post('visible_in_nav_cms_cascade') == 1)
					{
						$child->visible_in_nav_cms = $this->page->children_visible_in_nav_cms;
					}
					if ($this->request->post('child_template_cascade') == 1)
					{
						$child->template_id = $child_template;
					}

					$child->save();
				}
			}

			// Log the action.
			$this->_log("Saved child page settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");
		}
	}

	/**
	 * Shows information about the page such as who created it, who last edited it.
	 * Doesn't include any settings which can be changed.
	 *
	 * @uses	Sledge_Controller::_authorization()
	 */
	public function action_information()
	{
		// Permissions check
		$this->_authorization('edit_page', $this->page);

		// Get the first version of the page.
		// This is used to display the author and creation time of the page.
		$first_version = $this->page
			->first_version();

		// Get a count of the number of versions for this page.
		// Only count the versions which have been published.
		$version_count = $this->page
			->versions
			->where('stashed', '=', FALSE)
			->where('published', '=', TRUE)
			->where('embargoed_until', '<=', Editor::live_time())
			->count_all();

		// Get the current version of the page to report when the page was last modified and by whom.
		$current_version = $this->page
			->version();

		$this->template = View::factory("$this->_view_directory/information", array(
			'created_by'		=>	$first_version->person->name,
			'created_time'		=>	$first_version->edited_time,
			'last_modified_by'	=>	$current_version->person->name,
			'last_modified_time'	=>	$current_version->edited_time,
			'version_count'		=>	$version_count,
		));
	}

	/**
	 * ** Edit page navigation settings. **
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *    * Visible in navigation
	 *    * Visible in CMS navigation
	 *  * Advanced:
	 *    * Parent page
	 *
	 * @uses	Sledge_Controller::_authorization()
	 * @uses	Sledge_Controller::_log()
	 */
	public function action_navigation()
	{
		// Permissions check
		// The need to have a minimum of being able to edit the basic navigation settings.
		// If they can't edit the basic settings they won't be able to edit the advanced settings either.
		$this->_authorization('edit_page_navigation_basic', $this->page);

		if ($this->_method === Request::GET)
		{
			$this->template = View::factory("$this->_view_directory/navigation", array(

			));
		}
		elseif ($this->_method === Request::POST)
		{
			// Reparenting the page?
			$parent_id = $this->request->post('parent_id');
			if ($parent_id AND $parent_id != $this->page->mptt->parent_id AND $parent_id != $this->page->id)
			{
				Request::factory('cms/page/move/' . $this->page->pk())->post(array('parent_id' => $parent_id))->execute();
			}

			$this->update_inherited_columns(array(
				'template_id', 'visible_in_nav', 'visible_in_nav_cms'
			));

			// Save the new settings.
			$this->page->save();

			// Log the action.
			$this->_log("Saved publishing settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");
		}
	}

	/**
	 * ** Edit page search settings. **
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *     * Keywords
	 *     * Description
	 *  Advanced:
	 *     * External indexing
	 *     * Internal indexing
	 *
	 * @uses	Sledge_Controller::_authorization()
	 * @uses	Sledge_Controller::_log()
	 */
	public function action_search()
	{
		// Check permissions
		$this->_authorization('edit_page_search_basic', $this->page);

		if ($this->_method === Request::GET)
		{
			// GET request - show the search settings template.
			$this->_template = View::factory("$this->_view_directory/search");
		}
		elseif ($this->_method === Request::POST)
		{
			// Log the action
			$this->_log("Saved search settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Get the POST data.
			$post = $this->request->post();

			// Update the basic settings.
			$this->page
				->values(array(
					'description'	=>	$post['description'],
					'keywords'	=>	$post['keywords']
				));

			// If the current user can edit the advanced settings then update the values for those as well.
			if ($this->auth->logged_in('edit_page_children_advanced'))
			{
				$this->page
					->values(array(
						'external_indexing'	=>	$post['external_indexing'],
						'internal_indexing'	=>	$post['internal_indexing']
					));
			}

			// Save the page.
			$this->page
				->save();
		}
	}

	/**
	 * ** Edit the page tags. **
	 *
	 * @uses	Sledge_Controller::_authorization()
	 */
	public function action_tags()
	{
		// Permissions check
		$this->_authorization('edit_page', $this->page);

		if ($this->_method === Request::GET)
		{
			$this->template = View::factory("$this->_view_directory/tags", array(
				'current_tags'	=>	$this->page->get_tags(NULL, false),
			));
		}
		elseif ($this->_method === Request::POST)
		{
			$tag = ORM::factory('Tag', array('path' => $this->request->post('tag')));
			$page = $this->page;
			$action = ($this->request->post('action') == 'add')? 'add' : 'remove';

			if ($action == 'add')
			{
				if ( ! $tag->loaded())
				{
					// Need to create the tag before applying it to the page.
					$path = $this->request->post('tag');

					// Split the path into bits so we can find the parent and name of the tag.
					$parts = explode('/', $path);

					// Name of the tag is the last section of the path.
					$name = array_pop($parts);

					// Put the remaining parts back together to get the path of the parent tag.
					$parent = implode("/", $parts);

					// Find the parent tag.
					$parent = ORM::factory('Tag', array('path' => $parent));

					// Create the tag.
					$tag = ORM::factory('Tag');
					$tag->path = $this->request->post('tag');

					if ($parent->loaded())
					{
						$tag->parent_id = $parent->id;
					}

					$tag->name = $name;
					$tag->type = 2;
					$tag->save();
				}

				// Apply the tag to the page.
				$page->add('tag', $tag);
			}
			elseif ($action == 'remove')
			{
				DB::delete('tags_applied')
					->where('object_type', '=', $page->get_object_type_id())
					->where('object_id', '=', $page->id)
					->where('tag_id', '=', $tag->id)
					->execute();
			}
		}
	}

	/**
	 * ** Edit page visibility settings. **
	 *
	 * Settings in this group:
	 *  * visible
	 *  * visible from
	 *  * visible to
	 *
	 * @uses	Sledge_Controller::_log()
	 * @uses	Sledge_Controller::_authorization();
	 */
	public function action_visibility()
	{
		// Permissions check.
		$this->_authorization('edit_page', $this->page);

		if ($this->_method === Request::GET)
		{
			// GET request - show the visiblity form.
			$this->_template = View::factory("$this->_view_directory/visibility");
		}
		elseif ($this->_method === Request::POST)
		{
			// POST request - save changes to page visibility settings.

			// Get the POST data
			$post = $this->request->post();

			// Log the action
			$this->_log("Updated visibility settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Update the page settings.
			$this->page
				->values(array(
					'visible_from'	=>	strtotime($post['visible_from']),
					'visible_to'	=>	isset($post['visible_to'])? strtotime($post['visible_to']) : NULL,
					'visible'		=>	$post['visible']
				))
				->save();
		}
	}
} // End Sledge_Controller_Cms_Page
