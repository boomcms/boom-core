<?php

class Boom_Controller_Cms_Person_Save extends Controller_Cms_Person
{
	public function before()
	{
		parent::before();

		$this->_csrf_check();
	}

	public function action_add()
	{
		$password = PasswordGenerator::factory()->get_password();
		$enc_password = $this->auth->hash($password);

		$this->edit_person->set('password', $enc_password);

		$this->edit_person
			->values($this->request->post(), array('name', 'email'))
			->create()
			->add_group($this->request->post('group_id'));

		if (isset($password))
		{
			$email = new Email_Newuser($this->edit_person, $password, $this->request);
			$email->send();
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