<?php

namespace Boom;

class Page
{
	/**
	 *
	 * @var \Model_Page
	 */
	protected $_model;

	/**
	 *
	 * @var \Model_Page_URL
	 */
	protected $_url;

	public function __construct(\Model_Page $model)
	{
		$this->_model = $model;
	}

	public function __get($name)
	{
		return $this->_model->$name;
	}

	public function __set($name, $value)
	{
		return $this->_model->$name = $value;
	}

	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->_model, $name), $arguments);
	}

	public function getChildOrderingPolicy()
	{
		return new Page\ChildOrderingPolicy($this->_model->children_ordering_policy);
	}

	/**
	 * Get a description for the page.
	 *
	 * If no description property is set then the standfirst is used instead.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$description = ($this->_model->description != null)? $this->_model->description : \Chunk::factory('text', 'standfirst', $this)->text();

		return \strip_tags($description);
	}

	public function getDefaultChildTemplateId()
	{
		if ($this->_model->children_template_id)
		{
			return $this->_model->children_template_id;
		}

		$parent = $this->_model->parent();
		return ($parent->grandchild_template_id != 0)? $parent->grandchild_template_id : $this->_model->version()->template_id;
	}

	public function getId()
	{
		return $this->_model->id;
	}

	public function getTitle()
	{
		return $this->_model->version()->title;
	}

	/**
	 *
	 * @return \DateTimeImmutable
	 */
	public function getVisibleFrom()
	{
		return new \DateTimeImmutable($this->_model->visible_from);
	}

	/**
	 *
	 * @return \DateTimeImmutable
	 */
	public function getVisibleTo()
	{
		return new \DateTimeImmutable($this->_model->visible_to);
	}

	/**
	 *
	 * @return boolean
	 */
	public function isVisible()
	{
		return $this->isVisibleAtTime(\Boom\Editor::instance()->getLiveTime());
	}

	/**
	 *
	 * @param int $unixTimestamp
	 * @return boolean
	 */
	public function isVisibleAtTime($unixTimestamp)
	{
		return ($this->_model->visible && $this->getVisibleFrom()->getTimestamp() <= $unixTimestamp && ($this->getVisibleTo()->getTimestamp() >= $unixTimestamp || $this->getVisibleTo()->getTimestamp() == 0));
	}

	/**
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
	public function setChildOrderingPolicy($column, $direction)
	{
		$ordering_policy = new \Boom\Page\ChildOrderingPolicy($column, $direction);
		$this->_model->children_ordering_policy = $ordering_policy->asInt();

		return $this;
	}

	/**
	 * Returns the Model_Page_URL object for the page's primary URI
	 *
	 * The URL can be displayed by casting the returned object to a string:
	 *
	 *		(string) $page->url();
	 *
	 *
	 * @return \Model_Page_URL
	 */
	public function url()
	{
		if ($this->_url === null)
		{
			$this->_url = \ORM::factory('Page_URL')
				->values(array(
					'location'		=>	$this->_model->primary_uri,
					'page_id'		=>	$this->_model->id,
					'is_primary'	=>	true,
				));
		}

		return $this->_url;
	}

	/**
	 *
	 * @return \Boom\Page
	 */
	public function parent()
	{
		return ($this->mptt->is_root())? $this : \Boom\Finder\Page::byId($this->_model->mptt->parent_id);
	}
}