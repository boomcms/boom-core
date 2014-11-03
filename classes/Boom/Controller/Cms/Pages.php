<?php

class Boom_Controller_Cms_Pages extends Boom_Controller
{
	public function before()
	{
		parent::before();

		$this->authorization('manage_pages');
	}

	public function action_index()
	{
		$pages = ORM::factory('Page')
			->where('deleted', '=', false)
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('lvl', '=', 1)
			->find_all();

		$this->template = View::factory('boom/pages/index', array(
			'pages'	=>	$pages,
		));
	}
}