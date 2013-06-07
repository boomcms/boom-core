<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package	BoomCMS/People
 * @category	Auth
 * @author	Rob Taylor
 */
class Boom_Auth_Boom extends Auth
{
	/**
	 *
	 * @var Model_Person
	 */
	protected $_person;

	protected function _login($person, $password = NULL, $remember = FALSE)
	{
		$this->_person = $person;

		/**
		 * Although it's slower, we the check password first before checking that the account is valid and not locked.
		 * It shouldn't cause too much of a time waste for genuine users but may slow down hack attempts.
		 */
		if ($this->check_password($password) AND $this->_person->loaded() AND $this->_person->enabled AND ! $this->_person->is_locked())
		{
			$this->complete_login();
			return TRUE;
		}
		elseif ( ! $this->_person->is_locked())
		{
			$this->_person->login_failed();
			return FALSE;
		}
	}

	public function complete_login()
	{
		// Store the person ID in the session data.
		$this->_session->set($this->_config['session_key'], $this->_person->id);
		$this->_person->complete_login();
	}

	public function force_login(Model_Person $person)
	{
		$this->_person = $person;

		return $this->complete_login();
	}

	public function hash_password($password)
	{
		if ( ! class_exists('PasswordHash'))
		{
			require Kohana::find_file('vendor', 'PasswordHash');
		}

		$hasher = new PasswordHash(8, false);
		return $hasher->HashPassword($password);
	}

	/**
	 * Implements [Auth::get_user()]
	 *
	 * Returns a [Model_Person] object which relates to the current active person.
	 * If the session is not authenticated then an empty [Model_Person] object will be returned.
	 *
	 * A single argument can be given, as required by [Auth::get_user()] but this will be ignored here.
	 *
	 * @param type $default
	 * @uses Auth_Boom::$_person
	 *
	 * @return Model_Person
	 */
	public function get_user($default = NULL)
	{
		if ($this->_person === NULL)
		{
			// Store the loaded person model in [Auth_Boom::$_person] to avoid multiple database queries.
			$this->_person = new Model_Person($this->_session->get($this->_config['session_key']));
		}

		// Return the person object for the current user.
		return $this->_person;
	}

	public function is_disabled()
	{
		return Arr::get($this->_config, 'disabled', FALSE);
	}

	/**
	 * Determines whether the current user is logged in, or has permission to perform a particular role.
	 *
	 * * When called with no arguments returns whether the user is logged in.
	 * * When called with one argument returns whether the user is allowed to perform that role globally.
	 * * When called with two arguments returns whether the user is allowed to perform the given role at a particular point in the page tree.
	 *
	 * When being called to check whether the user can perform a role the name of the role can be given or a Model_Role object:
	 *
	 *		// Check that the current user has permission to 'do_stuff'
	 *		Auth::instance()->logged_in('do_stuff');
	 *
	 *		// Can also be called as:
	 *		Auth::instance()->logged_in(
	 *			ORM::factory('Role', array('name' => 'do_stuff'))
	 *		);
	 *
	 *
	 * @param mixed $role
	 * @param Model_Page $page
	 *
	 * @uses Auth_Bool::get_user()
	 * @uses Model_Person::is_allowed()
	 *
	 * @return boolean
	 */
	public function logged_in($role = NULL, $page = NULL)
	{
		if ($this->is_disabled())
		{
			return TRUE;
		}

		// Get the logged in person.
		$person = $this->get_user();

		if ($role === NULL)
		{
			// No role has been given, merely verify that the session is authenticated.
			// This can be done by checking whether the session's person object relates to a valid person.
			return $person->loaded();
		}
		else
		{
			// A role has been given - check whether the active person is allowed to perform the role.

			/**
			 * If a page has been given then add 'p_' to the role name.
			 *
			 * Roles which are applied to pages are prefixed with 'p_' in the database
			 * so that we can display them seperately from the general permissions when adding roles to groups in the people manager.
			 *
			 * To avoid having to add p_ to the start of role names everywhere in the code we just add the prefix here.
			 */
			if ($page !== NULL)
			{
				$role = 'p_'.$role;
			}

			// Does the person have the role at the specified page?
			return $person->is_allowed($role, $page);
		}
	}

	public function login($person, $password, $remember = FALSE)
	{
		if ( ! $password)
		{
			return FALSE;
		}

		if ( ! is_object($person) AND ! $person instanceof Model_Person)
		{
			// If we haven't been called with a person object then assume it's an email address
			// and get the person from the database.
			$person = new Model_Person(array('email' => $person));
		}

		return $this->_login($person, $password, $remember);
	}

	public function login_methods()
	{
		return Arr::get($this->_config, 'login_methods');
	}

	public function login_method_available($method)
	{
		return Arr::get($this->login_methods(), $method) !== NULL;
	}

	/**
	 * Required by [Auth] but we don't use because password validation is done by OpenID.
	 *
	 */
	public function password($username) {}

	public function check_password($password)
	{
		if ( ! class_exists('PasswordHash'))
		{
			require Kohana::find_file('vendor', 'PasswordHash');
		}

		$hasher = new PasswordHash(8, false);

		/*
		 * Create a dummy password to compare against if the user doesn't exist.
		 * This wastes CPU time to protect against probing for valid usernames.
		 */
		$hash = ($this->_person->password)? $this->_person->password : '$2a$08$1234567890123456789012';

		return $hasher->CheckPassword($password, $hash) AND $this->_person->loaded();
	}
}