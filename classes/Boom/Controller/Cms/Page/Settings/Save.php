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
			->values($this->request->post(), array('internal_name'))
			->update();
	}

	/**
	 * **Save the child page settings.**
	 *
	 * @uses Model_Page::cascade_to_children()
	 * @uses Model_Page::get_child_ordering_policy()
	 * @uses	Boom_Controller::log()
	 */
	public function action_children()
	{
		// Call the parent function to do the permissions check.
		parent::action_children();

		// Get the POST data so we don't have to call Request::post() multiple times.
		$post = $this->request->post();

		// Log the action.
		$this->log("Saved child page settings for page ".$this->page->version()->title." (ID: ".$this->page->id.")");

		// Which columns to expect for Model_Page::values();
		$expected = array('children_template_id');

		// Which columns to expect for Model_Page::children_cascade()
		$cascade_expected = array('template_id');

		// Set the new advanced settings, if allowed.
		if ($this->allow_advanced)
		{
			// Add the advanced settings to the expected values.
			$expected = array_merge($expected, array(
				'children_url_prefix',
				'children_visible_in_nav',
				'children_visible_in_nav_cms',
				'grandchild_template_id'
			));

			$cascade_expected = array_merge($cascade_expected, array('visible_in_nav', 'visible_in_nav_cms'));
		}

		// Update the page's child ordering policy, if required.
		if (isset($post['children_ordering_policy']) AND isset($post['children_ordering_direction']))
		{
			$this->page->set_child_ordering_policy($post['children_ordering_policy'], $post['children_ordering_direction']);
		}

		// Make the changes to the page.
		$this->page
			->values($post, $expected)
			->update();

		// Cascade any settings to the child pages, if required.
		if (isset($post['cascade']) AND ! empty($post['cascade']))
		{
			$this->page->cascade_to_children($post['cascade'], $cascade_expected);
		}
	}

	/**
	 * **Save page navigation settings.**
	 *
	 *
	 * @uses	Boom_Controller::log()
	 * @uses	Model_Page_MPTT::move_to_last_child()
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
				}
			}
		}

		// Log the action.
		$this->log("Saved navigation settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

		// Update the visible_in_nav and visible_in_nav_cms settings.
		$this->page
			->values($post, array('visible_in_nav', 'visible_in_nav_cms'))
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

		// Update the basic settings.
		$expected = array('description', 'keywords');

		// If the current user can edit the advanced settings then update the values for those as well.
		if ($this->allow_advanced)
		{
			$expected = array_merge($expected, array('external_indexing', 'internal_indexing'));
		}

		// Save the page.
		$this->page
			->values($this->request->post(), $expected)
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
