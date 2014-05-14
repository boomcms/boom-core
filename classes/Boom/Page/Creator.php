<?php

namespace Boom\Page;

use \Boom\Page as Page;

class Creator
{
	/**
	 *
	 * @var Model_Person
	 */
	protected $_creator;

	/**
	 *
	 * @var \Model_Page
	 */
	protected $_parent;

	protected $_templateId;
	protected $_title = 'Untitled';

	public function __construct(Page $parent, \Model_Person $creator)
	{
		$this->_parent = $parent;
		$this->_creator = $creator;
	}

	protected function _createPage()
	{
		$model = new \Model_Page;

		return $model
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

	protected function _createVersion(\Model_Page $page)
	{
		return \ORM::factory('Page_Version')
			->values(array(
				'edited_by'	=>	$this->_creator->id,
				'page_id'		=>	$page->getId(),
				'template_id'	=>	$this->_getTemplateId(),
				'title'			=>	$this->_title,
				'published' => true,
				'embargoed_until' => time(),
			))
			->create();
	}

	public function execute()
	{
		\Database::instance()->begin();

		$page = $this->_createPage();
		$this->_createVersion($page);
		$this->_insertIntoTree($page);

		\Database::instance()->commit();

		return $page;
	}

	protected function _insertIntoTree(\Model_Page $page)
	{
		$page->mptt->id = $page->getId();
		$page->mptt->insert_as_last_child($this->_parent->mptt);
	}

	protected function _getTemplateId()
	{
		if ($this->_templateId) {
			return $this->_templateId;
		}

		return $this->_parent->getDefaultChildTemplateId();
	}

	public function setTemplateId($template_id)
	{
		$template_id && $this->_templateId = $template_id;

		return $this;
	}

	public function setTitle($title)
	{
		$title && $this->_title = $title;

		return $this;
	}
}