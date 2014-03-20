<?php

class Boom_Link_Internal extends Link
{
	/** 
	 *
	 * @var Model_Page 
	 */
	protected $page;

	public function __construct($link)
	{
		$this->page = ctype_digit($link)? new Model_Page($link) : ORM::factory('Page_URL', array('location' => $link))->page;
	}

	public function get_page()
	{
		return $this->page;
	}

	public function url()
	{
		return $this->page->url();
	}
}