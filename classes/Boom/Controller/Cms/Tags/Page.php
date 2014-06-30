<?php

class Boom_Controller_Cms_Tags_Page extends Controller_Cms_Tags
{
	public function before()
	{
		parent::before();

		$this->model = \Boom\Page\Factory::byId($this->request->param('id'));
		$this->ids = array($this->model->id);

		// Before allowing viewing or editing of page tags check for that the current user has the 'edit_page' role for this page.
		$this->authorization('edit_page', $this->model);
	}

	public function action_list()
	{
		parent::action_list();

		$message = (count($this->tags))? 'page.hastags' : 'page.notags';
		$this->template->set('message', Kohana::message('boom', $message));
	}
}