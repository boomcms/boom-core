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

	public function deleteDrafts()
	{
		$commander = new \Boom\Page\Commander($this);
		return $commander
			->addCommand(new \Boom\Page\Delete\Drafts)
			->execute();
	}

	public function getChildOrderingPolicy()
	{
		return new Page\ChildOrderingPolicy($this->_model->children_ordering_policy);
	}

	public function getCreatedBy()
	{
		return $this->_model->created_by;
	}

	public function getCurrentVersion()
	{
		return $this->_model->version();
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
		return ($parent->grandchild_template_id != 0)? $parent->grandchild_template_id : $this->_model->getTemplateId();
	}

	public function getFeatureImage()
	{
		return \Boom\Asset::factory($this->_model->version()->feature_image);
	}

	public function getFeatureImageId()
	{
		return $this->_model->version()->feature_image_id;
	}

	public function getId()
	{
		return $this->_model->id;
	}

	/**
	 *
	 * @return \Boom\Page\Keywords
	 */
	public function getKeywords()
	{
		$keywords = explode(',', $this->_model->keywords);

		foreach ($keywords as &$keyword) {
			$keyword = trim($keyword);
		}

		return new Page\Keywords($keywords);
	}

	public function getTemplate()
	{
		return $this->_model->version()->template;
	}

	public function getTemplateId()
	{
		return $this->_model->version()->template_id;
	}

	public function getThumbnail()
	{
		
	}

	public function isDeleted()
	{
		return $this->_model->deleted;
	}

	public function loaded()
	{
		return $this->_model->loaded();
	}

	public function getTitle()
	{
		return $this->_model->version()->title;
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getVisibleFrom()
	{
		return new \DateTime('@' . $this->_model->visible_from);
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getVisibleTo()
	{
		return new \DateTime('@' . $this->_model->visible_to);
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasFeatureImage()
	{
		return $this->getFeatureImageId() != 0;
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

	public function wasCreatedBy(Model_Person $person)
	{
		return $this->getCreatedBy() === $person->id;
	}
}