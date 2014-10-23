<?php

namespace Boom\Auth;

use \PasswordHash;
use \Session;

class Auth
{
	/**
	 *
	 * @var array
	 */
	protected $config;

	protected static $instance;

	/**
	 *
	 * @var Boom\Person
	 */
	protected $person;

	/**
	 *
	 * @var type Session
	 */
	protected $session;

	protected $sessionKey = 'boomPersonId';

	protected $permissions_cache = array();

	public function __construct($config = array(), Session $session)
	{
		$this->config = $config;
		$this->session = $session;
	}

	protected function _login($person, $password = null, $remember = false)
	{
		$this->person = $person;

		/**
		 * Although it's slower, we the check password first before checking that the account is valid and not locked.
		 * It shouldn't cause too much of a time waste for genuine users but may slow down hack attempts.
		 */
		if ($this->check_password($password) && $this->person->loaded() && $this->person->enabled && ! $this->person->is_locked())
		{
			$this->complete_login($this->person);
			$remember === true && $this->_remember_login();

			return true;
		}
		elseif ($this->person->loaded() && ! $this->person->is_locked())
		{
			$this->person->login_failed();
			return false;
		}
	}

	public function cache_permissions($page)
	{
		$permissions = DB::select('roles.name', array('page_mptt.id', 'page_id'), array(DB::expr("bit_and(allowed)"), 'allowed'))
			->from('people_roles')
			->where('person_id', '=', $this->person->id)
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
			$this->permissions_cache[md5($p['name']. (int) $p['page_id'])] = $p['allowed'];
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
		$this->person = $person;

		return $this->complete_login($this->person);
	}

	public function hash($password)
	{
		if ( ! class_exists('PasswordHash'))
		{
			require \Kohana::find_file('vendor', 'PasswordHash');
		}

		$hasher = new PasswordHash(8, false);
		return $hasher->HashPassword($password);
	}

	public function getPerson()
	{
		if ($this->person === null) {
			$personId = $this->session->get($this->sessionKey);

			if ($personId) {
				return Person\Factory::byId($personId);
			} else {

			}
		}
	}

	public static function instance()
	{
		if (static::$instance === null) {
			static::$instance = new static(array(), Session::instance());
		}

		return static::$instance;
	}

	public function isLoggedIn()
	{
		if ($this->isDisabled()) {
			return true;
		}

		if ($this->person === null) {
			$this->person = $this->getPerson();
		}

		return $this->person;
	}

	public function isDisabled()
	{
		return isset($this->config['disabled']) && $this->config['disabled'] === true;
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

	protected function _remember_login()
	{
		// Token data
		$data = array(
			'user_id'    => $this->person->id,
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
			require \Kohana::find_file('vendor', 'PasswordHash');
		}

		$hasher = new PasswordHash(8, false);

		/*
		 * Create a dummy password to compare against if the user doesn't exist.
		 * This wastes CPU time to protect against probing for valid usernames.
		 */
		$hash = ($this->person->password)? $this->person->password : '$2a$08$1234567890123456789012';

		return $hasher->CheckPassword($password, $hash) && $this->person->loaded();
	}
}
