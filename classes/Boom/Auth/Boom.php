<?php

class Boom_Auth_Boom extends Auth_ORM
{
	/**
	 *
	 * @var Model_Person
	 */
	protected $_person;

	protected $_permissions_cache = array();

	protected function _login($person, $password = null, $remember = false)
	{
		$this->_person = $person;

		/**
		 * Although it's slower, we the check password first before checking that the account is valid and not locked.
		 * It shouldn't cause too much of a time waste for genuine users but may slow down hack attempts.
		 */
		if ($this->check_password($password) && $this->_person->loaded() && $this->_person->enabled && ! $this->_person->is_locked())
		{
			$this->complete_login($this->_person);
			$remember === true && $this->_remember_login();

			return true;
		}
		elseif ($this->_person->loaded() && ! $this->_person->is_locked())
		{
			$this->_person->login_failed();
			return false;
		}
	}

	public function cache_permissions($page)
	{
		$permissions = DB::select('roles.name', array('page_mptt.id', 'page_id'), array(DB::expr("bit_and(allowed)"), 'allowed'))
			->from('people_roles')
			->where('person_id', '=', $this->_person->id)
			->join('roles', 'inner')
			->on('people_roles.role_id', '=', 'roles.id')
			->group_by('role_id')
			->join('page_mptt', 'left')
			->on('people_roles.page_id', '=', 'page_mptt.id')
			->or_where_open()
				->and_where_open()
					->where('lft', '<=', $page->getMptt()->lft)
					->where('rgt', '>=', $page->getMptt()->rgt)
					->where('scope', '=', $page->getMptt()->scope)
				->and_where_close()
				->or_where('people_roles.page_id', '=', null)
			->or_where_close()
			->execute()
			->as_array();

		foreach ($permissions as $p)
		{
			$this->_permissions_cache[md5($p['name']. (int) $p['page_id'])] = $p['allowed'];
		}

		return $this;
	}

	public function complete_login($person)
	{
		// Store the person ID in the session data.
		$this->_session->set($this->_config['session_key'], $person->id);
		$person->complete_login();
	}

	public function force_login($person, $mark_as_forced = false)
	{
		$this->_person = $person;

		return $this->complete_login($this->_person);
	}

	public function hash($password)
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
	public function get_user($default = null)
	{
		if ($this->_person === null)
		{
			// Store the loaded person model in [Auth_Boom::$_person] to avoid multiple database queries.
			$this->_person = new Model_Person($this->_session->get($this->_config['session_key']));
		}

		// Return the person object for the current user.
		return $this->_person;
	}

	public function has_role(Model_Person $person, Model_Role $role, \Boom\Page $page = null)
	{
		$query = DB::select(array(DB::expr("bit_and(allowed)"), 'allowed'))
			->from('people_roles')
			->where('person_id', '=', $person->id)
			->where('role_id', '=', $role->id)
			->group_by('person_id');	// Strange results if this isn't here.

		if ($page !== null)
		{
			$query
				->join('page_mptt', 'left')
				->on('people_roles.page_id', '=', 'page_mptt.id')
				->where('lft', '<=', $page->getMptt()->lft)
				->where('rgt', '>=', $page->getMptt()->rgt)
				->where('scope', '=', $page->getMptt()->scope);
		}
		else
		{
			$query->where('people_roles.page_id', '=', 0);
		}

		$result = $query
			->execute()
			->as_array();

		return  ( ! empty($result) && (boolean) $result[0]['allowed']);
	}

	public function is_disabled()
	{
		return Arr::get($this->_config, 'disabled', false);
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
	 * @return boolean
	 */
	public function logged_in($role = null, $page = null)
	{
		if ($this->is_disabled())
		{
			return true;
		}

		// Get the logged in person.
		$person = $this->get_user();

		if ($role === null)
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
			if ($page !== null && is_string($role))
			{
				$role = 'p_'.$role;
			}

			if (is_string($role)) {
				$role = new Model_Role(array('name' => $role));
			}

			// Does the person have the role at the specified page?
			$page_id = ($page)? $page->getId() : 0;
			$cache_key = md5($role.$page_id);
			return isset($this->_permissions_cache[$cache_key])? $this->_permissions_cache[$cache_key] : $this->_permissions_cache[$cache_key] = $this->has_role($person, $role, $page);
		}
	}

	public function login($person, $password, $remember = false)
	{
		if ( ! $password)
		{
			return false;
		}

		if ( ! is_object($person) && ! $person instanceof Model_Person)
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
		return in_array($method, $this->login_methods());
	}

	/**
	 * Required by [Auth] but we don't use because password validation is done by OpenID.
	 *
	 */
	public function password($username) {}

	protected function _remember_login()
	{
		// Token data
		$data = array(
			'user_id'    => $this->_person->id,
			'expires'    => time() + $this->_config['lifetime'],
			'user_agent' => sha1(Request::$user_agent),
		);

		// Create a new autologin token
		$token = ORM::factory('User_Token')
			->values($data)
			->create();

		// Set the autologin cookie
		Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
	}

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

		return $hasher->CheckPassword($password, $hash) && $this->_person->loaded();
	}
}
