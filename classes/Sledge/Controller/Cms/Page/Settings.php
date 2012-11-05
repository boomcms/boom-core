<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Functions to edit page settings.
 * Each function relates to a view in the sledge/editor/page/settings directory.
 * When called without POST data the functions will display the relevant view. When called with POST data the controller will save the data in that view.
 * The controllers are tied quite strictly to the view so that the controller will only try and save what it expects to be in the view.
 * Therefore when adding a new setting it will need to be added to the view and to the controller.
 * This is done so that the controllers and quickly save their settings without having to check what data has been sent to determine what they need to do.
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
	protected $parent;

	public function before()
	{
		parent::before();

		// Check that the user can edit this page.
		if ( ! $this->auth->logged_in('edit', $this->page))
		{
			throw new HTTP_Exception_403;
		}
	}

	public function action_admin()
	{
		if ($this->request->method() == Request::POST)
		{
			$this->update_columns(array(
				'internal_name'
			));
			$this->page->save();

			// Ensure slot data doesn't get lost.
			Request::factory('cms/page/save_slots/' . $this->page->id)->execute();

			// Log the action.
			Sledge::log("Saved admin settings for page " . $this->page->title . " (ID: " . $this->page->id . ")");
		}
		else
		{
			$this->template = 'admin';
		}
	}

	public function action_children()
	{
		if ($this->request->method() == Request::POST)
		{
			$this->update_inherited_columns(array(
				'children_visible_in_leftnav', 'children_visible_in_leftnav_cms'
			));

			$this->page->order_children( (int) $this->request->post('child_ordering_policy'), $this->request->post('child_ordering_direction'));

			// These settings aren't inherited
			$this->update_columns(array(
				'default_grandchild_template_id', 'default_child_template_id', 'default_child_uri_prefix'
			));

			$this->page->save();

			// Updated existing child pages with leftnav visibility or template.
			if ($this->request->post('visible_in_leftnav_cascade') == 1 OR 
				$this->request->post('visible_in_leftnav_cms_cascase') == 1 OR
				$this->request->post('child_template_cascade') == 1
			)
			{
				$child_template = ($this->page->default_child_template_id == 0)? $this->page->template_id : $this->page->default_child_template_id;

				$children = DB::select('page_mptt.id')
					->from('page_mptt')
					->where('scope', '=', $this->page->mptt->scope)
					->where('lft', '>', $this->page->mptt->lft)
					->where('rgt', '<', $this->page->mptt->rgt)
					->execute();

				foreach ($children as $child)
				{
					$child = ORM::factory('Page', $child['id']);

					if ($this->request->post('visible_in_leftnav_cascade') == 1)
					{
						$child->visible_in_leftnav = $this->page->children_visible_in_leftnav;
					}
					if ($this->request->post('visible_in_leftnav_cms_cascade') == 1)
					{
						$child->visible_in_leftnav_cms = $this->page->children_visible_in_leftnav_cms;
					}
					if ($this->request->post('child_template_cascade') == 1)
					{
						$child->template_id = $child_template;
					}

					$child->save();

					// Import slot data from the previous version.
					Request::factory('cms/page/save_slots/' . $child->id)->execute();
				}
			}

			// Ensure slot data doesn't get lost.
			Request::factory('cms/page/save_slots/' . $this->page->id)->execute();

			// Log the action.
			Sledge::log("Saved child page settings for page " . $this->page->title . " (ID: " . $this->page->id . ")");
		}
		else
		{
			$default_child_template = ($this->page->default_child_template_id != 0)? $this->page->default_child_template_id : $this->page->template_id;
			$default_grandchild_template = ($this->page->default_grandchild_template_id != 0)? $this->page->default_grandchild_template_id : $this->page->template_id;

			$this->template = View::factory('sledge/editor/page/settings/children');
			$this->template->default_child_template = $default_child_template;
			$this->template->default_grandchild_template = $default_grandchild_template;
			$this->template->page = $this->page;
		}
	}

	public function action_feature()
	{
		if ($this->request->method() == Request::POST)
		{
			$this->update_columns(array('feature_image_id'));
			$this->page->save();

			// Ensure slot data doesn't get lost.
			Request::factory('cms/page/save_slots/' . $this->page->id)->execute();

			Sledge::log("Saved feature image for page " . $this->page->title . " (ID: " . $this->page->id . ")");
		}
		else
		{
			$this->template = 'feature';
		}
	}

	public function action_information()
	{
		// There's nothing to change in the page information view so only display the view.
		$this->template = 'information';
	}

	public function action_publishing()
	{
		if ($this->request->method() == Request::POST)
		{
			// Reparenting the page?
			$parent_id = $this->request->post('parent_id');
			if ($parent_id AND $parent_id != $this->page->mptt->parent_id AND $parent_id != $this->page->id)
			{
				Request::factory('cms/page/move/' . $this->page->pk())->post(array('parent_id' => $parent_id))->execute();
			}

			$this->update_inherited_columns(array(
				'template_id', 'visible_in_leftnav', 'visible_in_leftnav_cms'
			));

			// Make the browser reload the page if the template has been changed.
			$reload = $this->page->changed('template_id');

			// Save the new settings.
			$this->page->save();

			// Ensure slot data doesn't get lost.
			Request::factory('cms/page/save_slots/' . $this->page->id)->execute();

			// Log the action.
			Sledge::log("Saved publishing settings for page " . $this->page->title . " (ID: " . $this->page->id . ")");

			if ($reload)
			{
				$this->response->body($this->page->url());
			}
		}
		else
		{
			$this->template = 'publishing';
		}
	}

	public function action_search()
	{
		if ($this->request->method() == Request::POST)
		{
			$this->update_columns(array(
				'description', 'keywords', 'indexed', 'hidden_from_search_results'
			));

			$this->page->save();

			// Ensure slot data doesn't get lost.
			Request::factory('cms/page/save_slots/' . $this->page->id)->execute();

			Sledge::log("Saved search settings for page " . $this->page->title . " (ID: " . $this->page->id . ")");
		}
		else
		{
			$this->template = 'search';
		}
	}

	/**
	* Edit the page's tags.  
	*
	* @throws HTTP_Exception_403
	* @uses Model_Page::add()
	* @uses Model_Page::get_tags()
	* @todo Only retrieve IDs of current tags instead of complete object.
	*/
	public function action_tags()
	{
		if ( ! $this->auth->logged_in('edit_tags', $this->page))
		{
			throw new HTTP_Exception_403;
		}

		if ($this->request->method() == Request::POST)
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
		else
		{
			$this->response->headers('Cache-Control', 'no-cache');
			$this->template = 'tags';
		}
	}

	public function action_visibility()
	{
		// Permissions check.
		if ( ! $this->auth->logged_in('edit_visibility', $this->page))
		{
			throw new HTTP_Exception_403;
		}

		if ($this->request->method() == Request::POST)
		{
			$visible_to = $this->request->post('visible_to');

			$this->page->visible_from = strtotime($this->request->post('visible_from'));
			$this->page->visible_to = ($visible_to != NULL)? strtotime($visible_to) : NULL;
			$this->page->visible = $this->request->post('visible');
			$this->page->save();
		}
		else
		{
			$this->template = 'visibility';
		}
	}

	/**
	 * Update page columns with values from the POST data.
	 * Checks that a column has been changed and that the current is allowed to change that column.
	 * Controller functions which change page columns should call this function with an array of column names.
	 *
	 * @param	array 	$columns	Array of column names.
	 */
	public function update_columns(array $columns)
	{
		foreach ($columns as $column)
		{
			$value = $this->request->post($column);

			// Check that the column has been changed and then check for the required permission.
			// Although with Kohana's ORM we don't really need to check for a changed value 
			// doing this means that we don't check for edit permissions on columns which aren't being changed.
			if ($this->page->$column != $value AND $this->auth->logged_in("edit_$column", $this->page))
			{
				$this->page->$column = $value;
			}
		}
	}

	/**
	 * Update page columns which are inherited from the parent page if no value is given.
	 *
	 * @param	array 	$columns	Array of column names.
	 */ 
	public function update_inherited_columns(array $columns)
	{
		foreach ($columns as $column)
		{
			$value = $this->request->post($column);

			if ($this->page->$column != $value AND $this->auth->logged_in("edit_$column", $this->page))
			{
				// If no value is given then inherit from the parent page.
				if ($value == "")
				{
					// Lazy-load the page parent.
					if ($this->parent === NULL)
					{
						$this->parent = $this->page->parent();
					}

					$this->page->$column = $parent->$column;
				}
				else
				{
					// A value has been given so 
					$this->page->$column = $value;
				}
			}
		}
	}

	public function after()
	{
		if ($this->request->method() == Request::GET AND ! $this->template instanceof View)
		{
			// Turn the view filename into a view object and add the current page object to the view.
			$this->template = View::factory('sledge/editor/page/settings/' . $this->template)
				->set('page', $this->page);
		}
		elseif ($this->request->method() == Request::POST AND ! $this->response->body())
		{
			$this->response->headers('content-type', 'application/json');
			$this->response->body(json_encode(
				array(
					'vid'		=>	$this->page->version->id,
					'status'	=>	View::factory('sledge/editor/page/status', array('page' => $this->page, 'auth' => $this->auth))->render(),
				)
			));
		}

		parent::after();
	}
} // End Sledge_Controller_Cms_Page
