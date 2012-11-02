<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package Sledge/Auth
 * @copyright 2012, Hoop Associates
 */
class Sledge_Auth_Hoop extends Auth_Hoop_Core
{
	/**
	 * A person can login to a Sledge CMS if they've been added to a group in the CMS.
	 *
	 * @param	Model_Person	$who
	 * @return 	boolean
	 */
	public function can_login(Model_Person $person, $password)
	{
		return parent::can_login($person, $password) AND $person->groups->count_all() > 0;
	}

	/**
	 * Code to run when the user get's their password wrong.
	 * Increments the consecutive failed login counter and locks the account if they've got the password wrong 5 times.
	 * This functionality is common to action_login() and action_verify_password()
	 *
	 * @param Model_Person $person
	 */
	public function incorrect_password(Model_Person $person)
	{
		$person->consecutive_failed_login_counter += 1;

		if ($person->consecutive_failed_login_counter >= 5)
		{
			$person->locked = TRUE;

			// We should also setup an email class which handles the templating and mailing.
			$subject = Kohana::$config->load('config')->get('client_name') . ' CMS: Account Locked';
			$message = View::factory('sledge/email/tpl_account_locked', array(
				'person' => $person
			));

			Email::factory($subject, $message)
				->to($person)
				->from('hoopmaster@hoopassociates.co.uk')
				->reply_to('mail@hoopassociates.co.uk')
				->send();
		}

		$person->save();
	}

	public function logged_in($role = NULL, Model_Page $page = NULL)
	{
		$logged_in = parent::logged_in();

		if ($role === NULL OR $logged_in === FALSE)
		{
			return $logged_in;
		}

		$cache = Cache::instance();
		$cache_key = "permissions_" . $this->_user->id . "_" . $role . "_" . (string) $page;

		if ( ! $result = $cache->get($cache_key))
		{
			$query = DB::select(array(DB::expr('bit_and(allowed)'), 'allowed'))
				->from('people_roles')
				->join('roles', 'inner')
				->on('roles.id', '=', 'people_roles.role_id')
				->where('person_id', '=', $this->_user->id)
				->and_where_open()
					->where('roles.name', '=', $role)
					->or_where('roles.name', '=', '*')
				->and_where_close();

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
							->where('roles.name', '=', '*')
						->or_where_close()
					->and_where_close();
			}

			$result = $query
				->execute()
				->as_array();

			$result = ( ! empty($result) AND (boolean) $result[0]['allowed']);

			// Only cache the result for 1 minute.
			// We don't want a long cache lifetime because permissions changes need to take effect quickly
			// But we could easily check the same permission multiple times in a request so we want to reduce the queries a little bit.
			// Using memcache is significantly faster than using Kohana's query cache.
			$cache->set($cache_key, $result, 60);
		}

		return $result;
	}

	/**
	 * Update a user's password.
	 *
	 * @param	Model_Person	$person		The person who's password is being updated.
	 * @param	string		$password	What to change the password to.
	 */
	protected function update_password($person, $password)
	{
		// Log the activity.
		Sledge::log("Reset their password");

		// Update the users' password.
		$person->password = $this->hash($password, $person->passwordSalt);
		$person->consecutive_failed_login_counter = 0;
		$person->locked = FALSE;
		$person->save();

		// Send an email notification that their password has been updated.
		$subject = Kohana::$config->load('config')->get('client_name') . ' CMS: Your password has been updated';
		$message = View::factory('sledge/email/password_updated');
		$message->person = $person;

		Email::factory($subject)
			->to($person)
			->from('hoopmaster@hoopassociates.co.uk')
			->reply_to('mail@hoopassociates.co.uk')
			->message($message)
			->send('text/html');
	}

	/**
	 * Has the person been verified by SMS?
	 *
	 * @param	Model_Person	$person	Person to check verification of.
	 * @return 	boolean
	 */
	public function verified(Model_Person $person)
	{
		// Guest users and users who haven't set their account to require SMS verification are verified by default.
		if ( ! $person->loaded() OR ! $person->sms_verification)
		{
			return TRUE;
		}

		// If there's no verification cookie set then they're not veified.
		if ( ! $cookie = Cookie::get('device_verified'))
		{
			return FALSE;
		}

		// Check that the value of the cookie is correct.
		return ($cookie == Kohana::cache('sms_device_verified:' . $person->id, NULL, Date::DAY * 30));
	}
} // End Sledge_Auth_Hoop
