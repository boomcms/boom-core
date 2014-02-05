<?php

class Boom_Controller_Cms_Page_Urls_View extends Controller_Cms_Page_Urls
{
	public function action_add()
	{
		$this->template = View::factory("$this->_view_directory/add", array(
			'page' => $this->page,
		));
	}

	public function action_move()
	{
		$this->template = View::factory("$this->_view_directory/move", array(
			'url'		=>	$this->page_url,
			'current'	=>	$this->page_url->page,
			'page'	=>	$this->page,
		));
	}
}