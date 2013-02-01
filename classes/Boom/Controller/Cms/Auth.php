<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Authentication controller which uses OpenID
 *
 * @package	BoomCMS/People
 * @category	Controllers
 * @author	Rob Taylor
 */
class Boom_Controller_Cms_Auth extends Controller
{
	/**
	 * Login controller
	 *
	 * @uses LightOpenID
	 * @uses Auth::login()
	 */
	public function action_login()
	{
		// Load the OpenID library.
		require Kohana::find_file('vendor/openid', 'openid');

		// Create an OpenID consumer
		$openid = new LightOpenID($_SERVER['HTTP_HOST']);

		/**
		 * $_GET['openid_mode'] will be present when the OpenID provider redirects back here
		 * to complete the login process.
		 *
		 * Therefore, if it's not present we need to send the user to the OpenID provider to be logged in.
		 */
		if ( ! $this->request->query('openid_mode'))
		{
			// Set which OpenID provider to user.
			$openid->identity = Kohana::$config->load('openid')->get('identity');

			// Ask the provider nicely to give use the user's email address and name.
			$openid->required = array('contact/email', 'namePerson');

			// Redirect the user to to the OpenID provider.
			$this->redirect($openid->authUrl());
		}
		else
		{
			// The user is here because they've been sent here by the OpenID provider.
			// Complete the login.

			// Validate the OpenID request.
			if ($openid->validate())
			{
				// Get the attributes from the request.
				// We need the email address to determine to associate with a person in the CMS database.
				$attrs = $openid->getAttributes();

				// Load the person with the given email address.
				$person = new Model_Person(array('email' => $attrs['contact/email']));

				// Does the person exist?
				if ( ! $person->loaded())
				{
					// No, the given email address isn't allowed to login to this CMS.
					// TODO: Make a nice 'go away' page.
					throw new Exception('Invalid email');
				}

				// If the OpenID provider has been nice enough to tell us the person's name
				// And their name hasn't been set in the CMS database (it doesn't have to be given to add a person to the database)
				// Then set the person's name.
				if (isset($attrs['namePerson']) AND $person->name == NULL)
				{
					// Update the person's name.
					$person
						->set('name', $attrs['namePerson'])
						->update();
				}

				// Called [Auth::login()] to complete the login.
				Auth::instance()->login($person, " ");

				// Write the session data.
				Session::instance()->write();

				// Login is finished, send them on their merry way.
				$this->redirect('/');
			}
			else
			{
				// OpenId request is invalid.
				// Redirect the user back here to start the login process again.
				$this->redirect('/cms/login');
			}
		}
	}

	/**
	 * Logout controller
	 *
	 * @uses Auth::logout()
	 */
	public function action_logout()
	{
		// Use [Auth::logout()] to do the logging out.
		Auth::instance()->logout(TRUE);

		// Send the user back to the homepage.
		$this->redirect('/');
	}
}