<?php

class Boom_Controller_Cms_Groups_View extends Controller_Cms_Groups
{
	public function action_add()
	{
		$this->template = View::factory("$this->_view_directory/add", array(
			'group' => new Model_Group,
		));
	}

	public function action_edit()
	{
		$v = View::factory("$this->_view_directory/edit", array(
			'group'		=>	$this->group,
			'general_roles'	=>	ORM::factory('Role')
				->where('name', 'not like', 'p_%')
				->order_by('description', 'asc')
				->find_all(),
			'page_roles'	=>	ORM::factory('Role')
				->where('name', 'like', 'p_%')
				->order_by('description', 'asc')
				->find_all(),
		));

		$this->_show($v);
	}

	public function action_list_roles()
	{
		$roles = $this->group->roles( (int) $this->request->query('page_id'));

		$roles = json_encode($roles);

		$this->response
			->headers('Content-Type', static::JSON_RESPONSE_MIME)
			->body($roles);
	}
}