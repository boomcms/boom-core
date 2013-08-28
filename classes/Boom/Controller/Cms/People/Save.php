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
			$email_body = View::factory('boom/email/newuser', array(
				'password' => $password,
				'person' => $this->edit_person,
				'request' => $this->request,
				'site_name' => Kohana::$config->load('boom')->get('site_name'),
			));

			Email::factory('CMS Account Created')
				->to($this->edit_person->email)
				->from(Kohana::$config->load('boom')->get('support_email'))
				->message(View::factory('boom/email', array(
					'request' => $this->request,
					'content' => $email_body,
				)), 'text/html')
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
		if ($person_ids = $this->request->post('people'))
		{
			foreach ($person_ids as $person_id)
			{
				$this->edit_person
					->where('id', '=', $person_id)
					->find();

				if ($this->edit_person->loaded())
				{
					$this->_do_delete();
				}
			}
		}
		else
		{
			$this->_do_delete();
		}
	}

	protected function _do_delete()
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