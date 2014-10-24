<?php

class Controller_Cms_Page_Settings_View extends Controller_Cms_Page_Settings
{

	public function action_admin()
	{
		parent::action_admin();

		$this->template = new View("$this->viewDirectory/admin", array(
			'page' => $this->page,
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

		$childOrderingPolicy = $this->page->getChildOrderingPolicy();

		// Create the main view with the basic settings
		$this->template = View::factory("$this->viewDirectory/children", array(
			'default_child_template'	=>	$this->page->getDefaultChildTemplateId(),
			'templates'			=>	\Boom\Template\Helpers::names(),
			'child_order_column'		=>	$childOrderingPolicy->getColumn(),
			'child_order_direction'	=>	$childOrderingPolicy->getDirection(),
			'allowAdvanced'		=>	$this->allowAdvanced,
		));

		// If we're showing the advanced settings then set the neccessary variables.
		if ($this->allowAdvanced)
		{
			// Add the view for the advanced settings to the main view.
			$this->template->set(array(
				'default_grandchild_template'	=>	($this->page->grandchild_template_id != 0)? $this->page->grandchild_template_id : $this->page->getTemplateId(),
				'page'					=>	$this->page,
				'templates'				=>	$templates,
			));
		}
	}

	public function action_feature()
	{
		parent::action_feature();

		$images_in_page = new \Boom\Page\AssetsUsed($this->page->getCurrentVersion());
		$images_in_page->setType(\Boom\Asset\Type::IMAGE);

		$this->template = new View("$this->viewDirectory/feature", array(
			'feature_image_id' => $this->page->getFeatureImageId(),
			'images_in_page' => $images_in_page->getAll(),
		));
	}

	/**
	 * ** View the page navigation settings.**
	 *
	 */
	public function action_navigation()
	{
		parent::action_navigation();

		$this->template = new View("$this->viewDirectory/navigation", array(
			'page' => $this->page,
			'allowAdvanced' => $this->allowAdvanced,
		));
	}

	public function action_search()
	{
		parent::action_search();

		$this->template = new View("$this->viewDirectory/search", array(
			'allowAdvanced' => $this->allowAdvanced,
			'page' => $this->page,
		));
	}

	public function action_sort_children()
	{
		parent::action_children();

		$finder = new \Boom\Page\Finder;

		$children = $finder
			->addFilter(\Boom\Page\Finder\Filter\ParentPage($this->page))
			->setLimit(50)
			->find();

		$this->template = new View("$this->viewDirectory/sort_children", array(
			'children' => $children
		));
	}

	public function action_visibility()
	{
		parent::action_visibility();

		$this->template = new View("$this->viewDirectory/visibility", array(
			'page' => $this->page,
		));
	}
}