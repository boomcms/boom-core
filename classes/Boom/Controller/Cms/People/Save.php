<?php

class Boom_Controller_Cms_People_Save extends Controller_Cms_People
{
	public function action_add()
	{
		if ($this->auth->login_method_available('password'))
		{
			$password = Text::random(NULL, 15);
			$enc_password = $this->auth->hash($password);
		}

		$this->edit_person
			->values($this->request->post(), array('name', 'email'));

		if (isset($password))
		{
			$this->edit_person->set('password', $enc_password);
		}

		$this->edit_person
			->create()
			->add_group($this->request->post('group_id'));

		if (isset($password))
		{
			Email::factory('CMS Account Created')
				->to($this->edit_person->email)
				->from('support@uxblondon.com')
				->message(View::factory('email/signup', array(
					'password' => $password,
					'person' => $this->edit_person
				)))
				->send();
		}
	}

	public function action_add_group()
	{
		$groups = $this->request->post('groups');

		foreach ($groups as $group_id)
		{
			$this->log("Added person $this->person->email to group with ID $group_id");
			$this->edit_person->add_group($group_id);
		}
	}

	public function action_delete()
	{
		$this->log("Deleted person with email address: ".$this->edit_person->email);
		$this->edit_person->delete();
	}

	public function action_remove_group()
	{
		$this->log("Edited the groups for person ".$this->edit_person->email);
		$this->edit_person->remove_group($this->request->post('group_id'));
	}

	public function action_save()
	{
		$this->log("Edited user $this->edit_person->email (ID: $this->edit_person->id) to the CMS");

		$this->edit_person
			->values($this->request->post(), array('name', 'enabled'))
			->update();
	}
}