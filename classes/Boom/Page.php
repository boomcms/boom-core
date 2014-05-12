<?php

namespace Boom;

class Page extends \Model_Page
{
	/**
	 *
	 * @var \Model_Page_URL
	 */
	protected $_url;

	public function getChildOrderingPolicy()
	{
		return new Page\ChildOrderingPolicy($this->children_ordering_policy);
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
		$description = ($this->description != null)? $this->description : \Chunk::factory('text', 'standfirst', $this)->text();

		return \strip_tags($description);
	}

	public function getDefaultChildTemplateId()
	{
		if ($this->children_template_id)
		{
			return $this->children_template_id;
		}

		$parent = $this->parent();
		return ($parent->grandchild_template_id != 0)? $parent->grandchild_template_id : $this->version()->template_id;
	}

	/**
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
	public function setChildOrderingPolicy($column, $direction)
	{
		$ordering_policy = new \Boom\Page\ChildOrderingPolicy($column, $direction);
		$this->children_ordering_policy = $ordering_policy->asInt();

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
					'location'		=>	$this->primary_uri,
					'page_id'		=>	$this->id,
					'is_primary'	=>	true,
				));
		}

		return $this->_url;
	}
}