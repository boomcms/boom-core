<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ##Controller to view the page settings forms.
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Settings_View extends Controller_Cms_Page_Settings
{
	/**
	 * ** View the page admin settings.**
	 *
	 */
	public function action_admin()
	{
		// Call the parent function to check permissions.
		parent::action_admin();

		// Display the admin settings form.
		$this->template = View::factory("$this->_view_directory/admin", array(
			'page'	=>	$this->page,
		));
	}

	/**
	 * ** View the child page settings.**
	 *
	 */
	public function action_children()
	{
		// Call the parent function to check permissions.
		parent::action_children();

		// Get the ID and names of all the templates in the database.
		// These are used by both the basic and the advanced settings.
		$templates = ||M::factory('Template')
			->names();

		// Get the current child ordering policy column and direction.
		list($child_order_colum, $child_order_direciton) = $this->page->get_child_ordering_policy();

		// Create the main view with the basic settings
		$this->template = View::factory("$this->_view_directory/children", array(
			'default_child_template'	=>	($this->page->children_template_id != 0)? $this->page->children_template_id : $this->page->version()->template_id,
			'templates'			=>	$templates,
			'child_order_column'		=>	$child_order_colum,
			'child_order_direction'	=>	$child_order_direciton,
			'allow_advanced'		=>	$this->allow_advanced,
		));

		// If we're showing the advanced settings then set the neccessary variables.
		if ($this->allow_advanced)
		{
			// Add the view for the advanced settings to the main view.
			$this->template->set(array(
				'default_grandchild_template'	=>	($this->page->grandchild_template_id != 0)? $this->page->grandchild_template_id : $this->page->version()->template_id,
				'page'					=>	$this->page,
				'templates'				=>	$templates,
			));
		}
	}

	/**
	 * ** View the page navigation settings.**
	 *
	 */
	public function action_navigation()
	{
		// Call the parent to do a do permissions check
		parent::action_navigation();

		// Show the navigation settings form.
		$this->template = View::factory("$this->_view_directory/navigation", array(
			'page'			=>	$this->page,
			'allow_advanced'	=>	$this->allow_advanced,
		));
	}

	/**
	 * ** View the page search settings. **
	 *
	 */
	public function action_search()
	{
		// Call the parent function for permissions check.
		parent::action_search();

		// Show the search settings template.
		$this->template = View::factory("$this->_view_directory/search", array(
			'allow_advanced'	=>	$this->allow_advanced,
			'page'			=>	$this->page,
		));
	}

	public function action_sort_children()
	{
		parent::action_children();

		$children = Finder::pages()
			->which_are_children_of_the_page_by_id($this->page->id)
			->sorted_by_manual_order()
			->get_results(50);

		$this->template = View::factory("$this->_view_directory/sort_children", array(
			'children' => $children
		));
	}

	/**
	 * ** View the page visibility settings. **
	 *
	 */
	public function action_visibility()
	{
		// Call the parent function to check permissions.
		parent::action_visibility();

		// GET request - show the visiblity form.
		$this->template = View::factory("$this->_view_directory/visibility", array(
			'page'	=>	$this->page,
		));
	}
} // End Boom_Controller_Cms_Page_Settings_View