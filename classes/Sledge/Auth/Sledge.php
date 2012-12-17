<?php defined('SYSPATH') OR die('No direct script access.');

class Sledge_Auth_Sledge extends Auth
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

	public function get_user()
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
			$person = $this->get_user();

			$query = DB::select(array(DB::expr('bit_and(allowed)'), 'allowed'))
				->from('people_roles')
				->join('roles', 'inner')
				->on('roles.id', '=', 'people_roles.role_id')
				->where('person_id', '=', $person->id)
				->where('roles.name', '=', $role);

			if ($page !== NULL)
			{
				$query
					->join('page_mptt', 'left')
					->on('people_roles.page_id', '=', 'page_mptt.id')
					->and_where_open()
						->where('lft', '<=', $page->mptt->lft)
						->where('rgt', '>=', $page->mptt->rgt)
						->where('scope', '=', $page->mptt->scope)
						->or_where_open()
							->where('people_roles.page_id', '=', 0)
						->or_where_close()
					->and_where_close();
			}

			$result = $query
				->execute()
				->as_array();

			$result =  (! empty($result) AND (boolean) $result[0]['allowed']);

			return $result;
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