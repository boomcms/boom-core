<?php

class Boom_Controller_Cms_Auth_Recover extends Controller_Cms_Auth
{
	public function before()
	{
		parent::before();

		if ( ! $this->auth->login_method_available('password'))
		{
			throw new HTTP_Exception_404;
		}
	}

	public function action_create_token()
	{
		if ( ! Security::check($this->request->post('csrf')))
		{
			throw new HTTP_Exception_500;
		}

		$person = new Model_Person(array('email' => $this->request->post('email')));

		if ( ! $person->loaded() OR ! $person->enabled)
		{
			$this->_display_form(array('error' => Kohana::message('auth', 'recover.errors.invalid_email')));
			return;
		}

		$token = ORM::factory('PasswordToken')
			->values(array(
				'person_id' => $person->id,
				'token' => sha1(uniqid(NULL, TRUE)),
				'expres' => $_SERVER['REQUEST_TIME'] + Date::HOUR
			))
			->create();

		Email::factory('CMS Password Reset')
			->to($person->email)
			->from('support@uxblondon.com')
			->message(new View('email/recovery', array(
				'person' => $person,
				'token' => $token,
				'request' => $this->request,
			)), 'text/html')
			->send();

		$this->_display_form(array('message' => Kohana::message('auth', 'recover.email_sent')));
	}

	public function action_set_password()
	{
		$token = new Model_PasswordToken(array('token' => $this->request->query('token')));

		if ( ! $token->loaded() OR $token->is_expired() OR $token->person->email != $this->request->query('email'))
		{
			if ($token->is_expired())
			{
				$token->delete();
			}

			$this->_display_form(array('error' => Kohana::message('auth', 'recover.errors.invalid_token')));
			return;
		}

		if ($this->request->post('password1') AND $this->request->post('password2'))
		{
			if ( ! Security::check($this->request->post('csrf')))
			{
				throw new HTTP_Exception_500;
			}

			if ($this->request->post('password1') != $this->request->post('password2'))
			{
				$this->_display_form(array('error' => Kohana::message('auth', 'recover.errors.password_mismatch')));
				return;
			}

			$hashed_password = $this->auth->hash($this->request->post('password1'));

			$token->person
				->set('password', $hashed_password)
				->update();

			DB::delete('password_tokens')
				->where('person_id', '=', $token->person->id)
				->execute();

			$this->auth->force_login($token->person);
			$this->redirect('/');
		}
		else
		{
			$this->_display_form(array('token' => $token, 'email' => $token->person->email));
		}
	}

	protected function _display_form($vars = array())
	{
		$vars['tab'] = 'reset';
		$this->_display_login_form($vars);
	}
}