<?php

class Boom_Page_Creator
{
	/**
	 *
	 * @var Model_Person
	 */
	protected $_creator;

	/**
	 *
	 * @var Model_Page
	 */
	protected $_parent;

	protected $_template_id;
	protected $_title = 'Untitled';

	public function __construct(Model_Page $parent, Model_Person $creator)
	{
		$this->_parent = $parent;
		$this->_creator = $creator;
	}

	protected function _create_page()
	{
		return ORM::factory('Page')
			->values(array(
				'visible_in_nav'				=>	$this->_parent->children_visible_in_nav,
				'visible_in_nav_cms'			=>	$this->_parent->children_visible_in_nav_cms,
				'children_visible_in_nav'		=>	$this->_parent->children_visible_in_nav,
				'children_visible_in_nav_cms'	=>	$this->_parent->children_visible_in_nav_cms,
				'visible_from'				=>	time(),
				'created_by'				=>	$this->_creator->id,
			))
			->create();
	}

	protected function _create_version(Model_Page $page)
	{
		return ORM::factory('Page_Version')
			->values(array(
				'edited_by'	=>	$this->_creator->id,
				'page_id'		=>	$page->id,
				'template_id'	=>	$this->_get_template_id(),
				'title'			=>	$this->_title,
				'published' => TRUE,
				'embargoed_until' => time(),
			))
			->create();
	}

	public function execute()
	{
		Database::instance()->begin();

		$page = $this->_create_page();
		$this->_create_version($page);
		$this->_insert_into_tree($page);

		Database::instance()->commit();

		return $page;
	}

	protected function _insert_into_tree(Model_Page $page)
	{
		$page->mptt->id = $page->id;
		$page->mptt->insert_as_last_child($this->_parent->mptt);
	}

	protected function _get_template_id()
	{
		if ($this->_template_id)
		{
			return $this->_template_id;
		}

		return $this->_parent->get_default_child_template_id();
	}

	public function set_template_id($template_id)
	{
		$template_id AND $this->_template_id = $template_id;

		return $this;
	}

	public function set_title($title)
	{
		$title AND $this->_title = $title;

		return $this;
	}
}