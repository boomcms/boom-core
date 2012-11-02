<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Controller_Cms_Account extends Sledge_Controller
{
	/**
	 * Show the user's profile.
	 */
	public function action_profile()
	{
		if ($this->request->method() === Request::POST)
		{
			// Don't allow modifying profile data while mimicking another user.
			// We don't want any accidents arrising from confusion.

			if ( ! $this->auth->is_mimicking())
			{
				if ($this->request->post('firstname'))
				{
					$this->person->firstname = $this->request->post('firstname');
				}

				if ($this->request->post('lastname'))
				{
					$this->person->lastname = $this->request->post('lastname');
				}

				$this->person->sms_verification = $this->request->post('sms_verification');

				if ($phone = $this->request->post('phone'))
				{
					$key = Session::instance()->get('key');
					$encrypt = new Encrypt($key, MCRYPT_MODE_NOFB, MCRYPT_RIJNDAEL_128);
					// Encrypt the phone number.
					$this->person->phone = $encrypt->encode(
						// Replace a leading 0 in the phone number with +44.
						str_replace("0", "+44", substr($phone, 0, 1)) . substr($phone, 1)
					);
				}

				$password = $this->request->post('password');
				$confirm = $this->request->post('confirm');

				if ($key = Session::instance()->get('key') AND $password)
				{
					// Validate that a new password has been given and that the passwords match.
					$validation = Validation::factory(array(
						'password1'	=>	$password,
						'password2'	=>	$confirm,
					))
						->rule('password1', 'not_empty')
						->rule('password2', 'matches', array(':validation', 'password1', 'password2'));

					if ( ! $validation->check())
					{
						// Turn the validation errors into a single string so they can all be displayed by the login page.
						$errors = array_values($validation->errors('form'));
						$message = implode("<br />", $errors);

						throw new Validation_Exception($validation, $message);
					}

					// If they've set their phone number then we need to re-encode the number since the password is the basis for the encryption key.
					if ($this->person->phone)
					{
						$encrypt = new Encrypt($key, MCRYPT_MODE_NOFB, MCRYPT_RIJNDAEL_128);
						$phone = $encrypt->decode($this->person->phone);

						$key = sha1($password . $this->person->passwordSalt);
						$encrypt = new Encrypt($key, MCRYPT_MODE_NOFB, MCRYPT_RIJNDAEL_128);

						$this->person->phone = $encrypt->encode($phone);
					}

					// Update the password
					$this->auth->update_password($this->person, $password);
				}

				$this->person->theme = $this->request->post('theme');
				$this->person->save();

				// Remove the encryption key from the session data.
				Session::instance()->delete('key');
			}

			if ($this->auth->logged_in('manage_people') AND $this->request->post('switch_user'))
			{
				$mimick_user = ORM::factory('Person', $this->request->post('switch_user'));

				if ($mimick_user->loaded())
				{
					$this->auth->mimick_user($mimick_user);
				}
			}
			elseif ($this->auth->is_mimicking())
			{
				 $this->auth->mimick_user(NULL);
			}
		}
		else
		{
			// Find the people who are added to to this CMS for the 'mimick user' select.
			// This can't be done as a subquery because the tables are in different databases.
			$subquery = DB::select('person_id')
				->from('people_groups')
				->distinct(TRUE)
				->execute()
				->as_array();

			$available = ORM::factory('Person')
				->where('deleted', '=', FALSE)
				->where('person.id', 'IN', $subquery)
				->where('person.id', '!=', $this->auth->get_real_user()->pk())
				->order_by('firstname', 'asc')
				->order_by('lastname', 'desc')
				->find_all();

			$this->template = View::factory('sledge/account/profile', array(
				'person'		=>	$this->person,
				'actual_person'	=>	$this->auth->get_real_user(),
				'people'		=>	$available,
				'mimicking'	=>	$this->auth->is_mimicking(),
			));
		}
	}

	/**
	 *
	 */
	public function action_verify_password()
	{
		$password = $this->request->post('password');

		if ( ! $this->auth->has_password($this->person, $password))
		{
			$this->auth->incorrect_password($this->person);

			throw new Sledge_Exception("Incorrect password");
		}

		$key = sha1($password . $this->person->passwordSalt);
		Session::instance()->set('key', $key);

		if ($this->person->sms_verification AND ! $this->auth->verified($this->person))
		{
			try
			{
				Request::factory('cms/auth/verify')
				->post(array(
					'person_id'	=>	$this->person->id,
					'code'		=>	$this->request->post('verification_code'),
				))
				->execute();
			}
			catch (Sledge_Exception $e)
			{
				Session::instance()->delete('key');
				throw $e;
			}

			if (! $this->request->post('verification_code'))
			{
				$this->response->body("sms_verification");
				return;
			}
		}

		// Set the encryption key for the the user's phone number.
		$encrypt = new Encrypt($key, MCRYPT_MODE_NOFB, MCRYPT_RIJNDAEL_128);
		$this->template = View::factory('sledge/account/security', array(
			'encrypt'	=>	$encrypt,
		));
	}
}
