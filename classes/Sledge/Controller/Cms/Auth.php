<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller to handle CMS authentication - login, logout, and password reset.
 * Most of the actual work is done by the Auth module but this controller adds extra stuff like logging and displaying the templates.
 *
 *  This controller extends Kohana controller because Controller requires a user to be logged in.
 * Which would just be silly here.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Controller_Cms_Auth extends Kohana_Controller
{
	/**
	 *
	 * @var	Auth
	 */
	protected $auth;

	public function before()
	{
		$this->auth = Auth::instance();
	}

	/**
	* Log a user into the CMS
	* @uses Auth::login()
	* @return void
	*/
	public function action_login()
	{
		// Check they're not already logged in.
		if ($this->auth->logged_in())
		{
			// You're already logged in dummy, just go away.
			$this->redirect("/");
		}

		// If the request method is post then process the form data.
		// If we didn't check the request method then error messages for empty email / password would appear when the user first opens the login form.
		if ($this->request->method() === Request::POST)
		{
			// Validate the form data.
			$validation = Validation::factory($this->request->post());
			$validation->rule('email', 'not_empty');
			$validation->rule('password', 'not_empty');

			// Does the form validate?
			if ( ! $validation->check())
			{
				// Turn the validation errors into a single string so they can all be displayed by the login page.
				$errors = array_values($validation->errors('form'));
				$message = implode("<br />", $errors);

				throw new Validation_Exception($validation, $message);
			}
			else
			{
				// Everything is filled in, lets try and log them in.
				$email		=	$this->request->post('email');
				$password	=	$this->request->post('password');
				$persist		=	($this->request->post('sledge-remember-me') == 'on')? TRUE : FALSE;

				// A nice little touch to save Hoop people's fingers.
				// If there's no @ in the email address add @hoopassociates.co.uk to the end.
				if ( ! strstr($email, '@'))
				{
					$email .= "@hoopassociates.co.uk";
				}

				// Do this now and we can pass it to Auth::login() so we only have to query the database once.
				$person = ORM::factory('Person', array('emailaddress' => $email));

				// Can the person login?
				// We want to check that the login is going to succeed before sending out a verification code through SMS
				if ($this->auth->can_login($person, $password))
				{
					// No password salt set?
					// Create a salt and re-hash their password.
					// Now is a good opportunity as we have the password in plain text from the form data.
					// This is done for backward compatability with accounts created by sledge2.
					if ($person->passwordSalt === NULL)
					{
						$person->passwordSalt = Text::random('alnum', 128);
						$person->password = $password;
					}

					$person->save();

					// Store their hashed password in the session data to use as an encryption key for storing data such as the user's phone number in the database.
					Session::instance()->set('key', sha1($password . $person->passwordSalt));

					if ((Kohana::$environment === Kohana::PRODUCTION OR Kohana::$environment === Kohana::STAGING) AND ! Auth::instance()->verified($person))
					{
						$this->action_verify($person);

						if ( ! $this->auth->verified($person))
						{
							// They're still not verified - they need to enter the verification code.
							$this->response
								->headers('content-type', 'application/json')
								->body(json_encode(array('sms_verification' => "")));

							return;
						}
					}

					// Log them in.
					$this->auth->login($person, $password, $persist);

					// Possible fix to "Error reading session data" bug
					// @link http://stackoverflow.com/questions/8374807/kohana-3-2-error-reading-session-data-debian-platform
					Session::instance()->write();

					// Delete the verification token.
					Kohana::cache('sms_verification_code:' . $person->id, NULL, 0);

					// Log the activity.
					Sledge::log("Logged in");

					// Set their consecutive failed login counter to 0.
					$person->consecutive_failed_login_counter = 0;
					$person->save();

					// Login valid? Redirect them.
					$this->response
						->headers('content-type', 'application/json')
						->body(json_encode(array('redirecturl' => "/")));
				}
				else
				{
					// If the account exists but they're using the wrong password, find out if this is an old password.
					if ($person->loaded() AND ! $this->auth->has_password($person, $password))
					{
						$this->auth->incorrect_password($person);

						$time = DB::select(array(DB::expr('max("audit_time")'), 'time'))
							->from('person_v')
							->where('rid', '=', $person->id)
							->where('password', '=', $this->auth->hash($password, $person->passwordSalt))
							->execute('hoopid')
							->get('time');

						// The password is an old one. When was it changed?
						if ($time)
						{
							// When was it changed?
							$changed = DB::select(array(DB::expr('min("audit_time")'), 'time'))
								->from('person_v')
								->where('rid', '=', $person->pk())
								->where('audit_time', '>', $time)
								->execute('hoopid')
								->get('time');

							// Put it in a human readable format as the time since now.
							$changed = Date::fuzzy_span($changed, time());

							throw new Sledge_Exception("Your password was changed $changed.<br /><a class=\"resetpasswordlink\" href=\"/cms/account/reset\">Not changed by you?</a>");
						}
					}

					throw new Sledge_Exception("We couldn't find your account. Please try again or <a class=\"resetpasswordlink\" href=\"/cms/account/reset\">click here</a> to reset your password.");
				}
			}
		}
	}

	/**
	 * Log the user out of the CMS
	 *
	 * @uses	Auth::logout()
	 */
	public function action_logout()
	{
		if ($this->auth->logged_in())
		{
			// Log it.
			Sledge::log("Logged out");

			$this->auth->logout(TRUE);
		}

		$this->redirect($this->request->referrer());
	}

	/**
	* Reset the user's CMS password, the muppet's forgotten it.
	*
	* @return void
	*/
	public function action_reset()
	{
		// See if there's been a reset token given by GET or POST.
		$token = ($this->request->method() === Request::POST)? $this->request->post('token') : $this->request->query('token');

		if ($this->request->method() === Request::POST OR $token)
		{
			$email = ($this->request->method() === Request::POST)? $this->request->post('email') : $this->request->query('email');

			$validation = Validation::factory(array('email' => $email));
			$validation->rule('email', 'not_empty');
			$validation->rule('email', 'email');

			// Does the form validate?
			if ( ! $validation->check())
			{
				// Turn the validation errors into a single string so they can all be displayed by the login page.
				$errors = array_values($validation->errors('form'));
				$message = implode("<br />", $errors);

				throw new Validation_Exception($validation, $message);
			}
			else
			{
				$person = ORM::factory('Person', array('emailaddress' => $email));

				if ($person->loaded())
				{
					if ($token)
					{
						$template = View::factory('sledge/account/login');

						// We've got the reset token - let's do some resetting.
						// Does the token exist?
						$data = Kohana::cache("password_reset_token:$token", NULL, 3600);

						if ( ! $data)
						{
							$template->message = 'Sorry, we were unable to find that reset token.';
						}
						else
						{
							// Double check that the information associated with the token hashes to the token ID.
							$token2 = $token = sha1($data['string'] . $data['time'] . $data['email']);

							// Is the token valid?
							if ($email == $data['email'] AND $token2 == $token)
							{
								// Get request: we're displaying the template for the first time.
								// POST request: create a new password.
								if ($this->request->method() === Request::POST)
								{

									// Validate that a new password has been given and that the passwords match.
									$validation = Validation::factory(array(
										'password1'	=>	$this->request->post('password'),
										'password2'	=>	$this->request->post('password2'),
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

									// Update the password
									$this->auth->update_password($person, $this->request->post('password1'));

									// Delete the token from cache.
									$deleted = Kohana::cache("password_reset_token:$token", "", 1);

									$this->response->body(json_encode(array('message' => 'Your password has been updated. Please login with your new password to continue.')));
									return;
								}
								else
								{
									$template->token = $this->request->query('token');
								}
							}
							else
							{
								$template->message = 'Invalid token';
							}
						}

						$this->response->body($template);
					}
					else
					{
						// Generate a random string.
						$string = Text::random('alnum', 10);

						// Is it home time yet?
						$time = time();

						// Encode is with the current time and users' email address to create a unique token.
						$token = sha1($string . $time . $person->emailaddress);

						// Stick it in the cache for an hour.
						$result = Kohana::cache("password_reset_token:$token", array('string' => $string, 'email' => $person->emailaddress, 'time' => $time), 3600);

						// Send an email with the reset link.
						$subject = Kohana::$config->load('config')->get('client_name') . ' CMS: Your password has been reset';
						$message = View::factory('sledge/email/login_reset');
						$message->person = $person;
						$message->token = urlencode($token);

						Email::factory($subject)
							->to($person)
							->from('hoopmaster@hoopassociates.co.uk')
							->reply_to('mail@hoopassociates.co.uk')
							->message($message)
							->send('text/html');

						$this->response->body( json_encode( array('message' => 'An email will be sent to you shortly with instructions on how to reset your password. If you do not receive it today, contact the Hoop team for assistance.')));
					}
				}
				else
				{
					throw new Sledge_Exception("Sorry, we don't seem to know that one. Either try again or contact the hoop team for assistance.");
				}
			}
		}
	}

	/**
	 * Send of check an SMS verification code
	 */
	public function action_verify($person = NULL)
	{
		if ( ! $person)
		{
			$person = ORM::factory('Person', $this->request->post('person_id'));
		}

		if ($person->loaded() AND $person->sms_verification AND $person->phone != "")
		{
			if ($this->request->post('code') == "")
			{
				// Has a verification code been sent in the past 60 seconds?
				$sent = Kohana::cache('login_verification_code:' . $person->id, NULL, 60);

				// Only send a verification code if we couldn't find an existing code.
				if ( ! $sent)
				{
					$encrypt = new Encrypt(Session::instance()->get('key'), MCRYPT_MODE_NOFB, MCRYPT_RIJNDAEL_128);
					$phone = $encrypt->decode($person->phone);

					// Generate an SMS verification token.
					$token = Twilio_SMS_Token::factory($phone);

					// Save the token.
					Kohana::cache('login_verification_code:' . $person->id, $token->code());
				}
			}
			elseif ($this->request->post('code') != Kohana::cache('login_verification_code:' . $person->id, 3600))
			{
				// They got the verification code wrong, tell them off.
				throw new Sledge_Exception("Invalid SMS verification code");
			}
			else
			{
				// They got the SMS verification right.

				// Are they remembering this device?
				if ($this->request->post('remember_device'))
				{
					// Generate a token.
					$token = Security::token();

					// Save it to cache.
					Kohana::cache('sms_device_verified:' . $person->id, $token, Date::DAY * 30);

					// Set a cookie.
					Cookie::set('device_verified', $token, Date::DAY * 30);
				}
			}
		}
	}

	public function after()
	{
		// A non-ajax request and no output has been sent?
		// We can't be having that, show the login form.
		if ( ! $this->response->body() AND ! $this->request->is_ajax() AND ! $this->request->is_external())
		{
			// Password reset form
			$template = View::factory('sledge/account/login');
			$this->response->body($template);
		}
	}
}
