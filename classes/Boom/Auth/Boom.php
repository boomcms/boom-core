<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Auth_Boom extends Auth
{
	protected $_person;

	/**
	 * The logging in has already been done by openid - just mark the session has logged in to this user.
	 *
	 */
	protected function _login($person, $password = NULL, $remember = FALSE)
	{
		if ( ! is_object($person) AND ! $person instanceof Model_Person)
		{
			$person = new Model_Person(array('email' => $person));
		}

		$this->_person = $person;
		$this->_session->set($this->_config['session_key'], $person->id);
	}

	public function get_user($default = NULL)
	{
		if ($this->_person === NULL)
		{
			$this->_person = new Model_Person($this->_session->get($this->_config['session_key']));
		}

		return $this->_person;
	}

	public function logged_in($role = NULL, $page = NULL)
	{
		if ($role === NULL)
		{
			return $this->get_user()->loaded();
		}
		else
		{
			// Does the person have the role at the specified page?
			return $this->get_user()
				->is_allowed($role, $page);
		}
	}

	/**
	 * Required by [Auth] but we don't use because password validation is done by OpenID.
	 *
	 */
	public function password($username) {}

	/**
	 * Required by [Auth] but we don't use because password validation is done by OpenID.
	 *
	 */
	public function check_password($username) {}
}