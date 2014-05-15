<?php

class Boom_Controller_Cms_Person_View extends Controller_Cms_Person
{
	public function action_add()
	{
		$this->template = View::factory($this->viewDirectory."new", array(
			'groups'	=>	ORM::factory('Group')->names(),
		));
	}

	public function action_add_group()
	{
		$groups = ORM::factory('Group')
			->names(
				DB::Select('group_id')
					->from('people_groups')
					->where('person_id', '=', $this->edit_person->id)
			);

		// Set the response template.
		$this->template = View::factory("$this->viewDirectory/addgroup", array(
			'person'	=>	$this->edit_person,
			'groups'	=>	$groups,
		));
	}

	public function action_view()
	{
		if ( ! $this->edit_person->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template = View::factory($this->viewDirectory."view", array(
			'person'		=>	$this->edit_person,
			'request'		=>	$this->request,
			'activities'	=>	$this->edit_person->logs->order_by('time', 'desc')->limit(50)->find_all(),
			'groups'		=>	$this->edit_person->groups->order_by('name', 'asc')->find_all(),
		));
	}
}