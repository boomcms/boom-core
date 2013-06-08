<?php

class Boom_Controller_Cms_Profile extends Boom_Controller
{
	public function action_view()
	{
		$v = new View('boom/account/profile', array(
			'person' => $this->person,
			'auth' => $this->auth,
			'logs' => $this->person->get_recent_account_activity(),
		));

		$this->response->body($v);
	}

	public function action_save()
	{
		extract($this->request->post());

		$this->person->set('name', $name);

		if ($current_password AND $new_password)
		{
			if ($this->auth->check_password($current_password))
			{
				$this->person->set('password', $this->auth->hash($new_password));
			}
			else
			{
				$this->response
					->status(500)
					->headers('Content-Type', 'application/json')
					->body(json_encode(array('message' => 'Invalid password')));
			}
		}

		$this->person->update();
	}
}