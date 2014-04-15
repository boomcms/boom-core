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
	    if (ctype_digit($link))
	    {
	        $this->page = new Model_Page($link);
	    }
	    else
	    {
	        $location = ($link === '/')? $link : substr($link, 1);
	        $this->page = ||M::factory('Page_URL', array('location' => $location))->page;
	    }
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
