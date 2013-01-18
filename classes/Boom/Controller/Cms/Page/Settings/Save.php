<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ##Controller to save changes to page settings.
 *
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Settings_Save extends Controller_Cms_Page_Settings
{
	/**
	 * **Save the page admin settings.**
	 *
	 * @uses	Boom_Controller::log()
	 */
	public function action_admin()
	{
		// Run the parent function for permissions checking.
		parent::action_admin();

		// Log the action.
		$this->log("Saved admin settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

		// Set the new page internal name and save the page.
		$this->page
			->values(array(
				'internal_name'		=>	$this->request->post('internal_name'),
			))
			->update();
	}

	/**
	 * **Save the child page settings.**
	 *
	 * @uses	Boom_Controller::log()
	 */
	public function action_children()
	{
		// Call the parent function to do the permissions check.
		parent::action_children();

		// Get the POST data.
		$post = $this->request->post();

		// Log the action.
		$this->log("Saved child page settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

		// Set the new advanced settings, if allowed.
		if ($this->allow_advanced)
		{
			$this->page
				->values($post, array(
					'children_visible_in_nav',
					'children_visible_in_nav_cms',
					'children_url_prefix'	,
					'grandchild_template_id'
				));

			// Updated existing child pages with leftnav visibility or template if required.
			if ($post['visible_in_nav_cascade'] == 1 OR
				$post['visible_in_nav_cms_cascase'] == 1 OR
				$post['child_template_cascade'] == 1
			)
			{
				if ($post['child_template_cascade'] == 1)
				{
					// Which template should be cascaded to the children?
					$child_template = ($this->page->children_template_id == 0)? $this->page->version()->template_id : $this->page->children_template_id;
				}

				$values = array();

				// Cascading the visible_in_nav setting?
				if ($post['visible_in_nav_cascade'] == 1)
				{
					$values['visible_in_nav'] = $this->page->children_visible_in_nav;
				}

				// Cascading the visible_in_nav_cms setting?
				if ($post['visible_in_nav_cms_cascade'] == 1)
				{
					$values['visible_in_nav_cms'] = $this->page->children_visible_in_nav_cms;
				}

				// Cascading the template setting?
				if ($post['child_template_cascade'] == 1)
				{
					$values['template_id'] = $child_template;
				}

				// Run the query
				DB::update('pages')
					->join('page_mptt', 'inner')
					->on('pages.id', '=', 'page_mptt.id')
					->where('parent_id', '=', $this->page->id)
					->set($values)
					->execute();
			}

			// Update the basic values and save the page.
			$this->page
				->children_ordering_policy($post['children_ordering_policy'], $post['child_ordering_direction'])
				->set('children_template_id', $post['children_template_id'])
				->update();
		}
	}

	/**
	 * **Save page navigation settings.**
	 *
	 *
	 * @uses	Boom_Controller::log()
	 * @uses	Model_Page_MPTT::move_tp_last_child()
	 * @uses	Model_Page::sort_children()
	 */
	public function action_navigation()
	{
		// Call the parent function to check permissions.
		parent::action_navigation();

		// Get the POST data.
		$post = $this->request->post();

		// Allow changing the advanced settings?
		if ($this->allow_advanced)
		{
			// Reparenting the page?
			// Check that the ID of the parent has been changed and the page hasn't been set to be a child of itself.
			if ($post['parent_id'] AND $post['parent_id'] != $this->page->mptt->parent_id AND $post['parent_id'] != $this->page->id)
			{
				// Check that the new parent ID is a valid page.
				$parent = new Model_Page($post['parent_id']);

				if ($parent->loaded())
				{
					// New parent is a valid page so update the page.
					// Move the page to be the last child of the new parent.
					$this->page
						->mptt
						->move_to_last_child($post['parent_id']);

					// Now sort the parent's children according to it's child ordering policy to move the new page into place.
					$parent->sort_children();
				}
			}
		}

		// Log the action.
		$this->log("Saved navigation settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

		// Update the visible_in_nav and visible_in_nav_cms settings.
		$this->page
			->values(array(
				'visible_in_nav'		=>	$post['visible_in_nav'],
				'visible_in_nav_cms'	=>	$post['visible_in_nav_cms'],
			))
			->update();
	}

	/**
	 * ** Save page search settings. **
	 *
	 * @uses	Boom_Controller::log()
	 */
	public function action_search()
	{
		// Call the parent function to check permissions.
		parent::action_search();

		// Log the action
		$this->log("Saved search settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

		// Get the POST data.
		$post = $this->request->post();

		// Update the basic settings.
		$this->page
			->values(array(
				'description'	=>	$post['description'],
				'keywords'	=>	$post['keywords']
			));

		// If the current user can edit the advanced settings then update the values for those as well.
		if ($this->allow_advanced)
		{
			$this->page
				->values(array(
					'external_indexing'	=>	$post['external_indexing'],
					'internal_indexing'	=>	$post['internal_indexing']
				));
		}

		// Save the page.
		$this->page
			->update();
	}

	/**
	 * ** Save page visibility settings. **
	 *
	 * @uses	Boom_Controller::log()
	 */
	public function action_visibility()
	{
		// Call the parent function to check permissions.
		parent::action_visibility();

		// Get the POST data
		$post = $this->request->post();

		// Log the action
		$this->log("Updated visibility settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

		// Update the page settings.
		$this->page
			->values(array(
				'visible_from'	=>	strtotime($post['visible_from']),
				'visible_to'	=>	isset($post['visible_to'])? strtotime($post['visible_to']) : NULL,
				'visible'		=>	$post['visible']
			))
			->update();
	}
} // End Boom_Controller_Cms_Page_Settings_Save