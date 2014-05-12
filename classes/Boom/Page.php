<?php

namespace Boom;

class Page extends \Boom\Model\Page
{
	/**
	 *
	 * @var \Model_Page_URL
	 */
	protected $_url;

	/**
	 * Get a description for the page.
	 *
	 * If no description property is set then the standfirst is used instead.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$description = ($this->description != null)? $this->description : Chunk::factory('text', 'standfirst', $this)->text();

		return \strip_tags($description);
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
			// Get the primary URL for this page.
			$this->_url = ORM::factory('Page_URL')
				->values(array(
					'location'		=>	$this->primary_uri,
					'page_id'		=>	$this->id,
					'is_primary'	=>	true,
				));
		}

		return $this->_url;
	}
}