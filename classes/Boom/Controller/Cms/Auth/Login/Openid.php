<?php

class Boom_Controller_Cms_Auth_Login_Openid extends Controller_Cms_Auth_Login
{
	/**
	 *
	 * @var LightOpenID
	 */
	public $openid;

	public $method = 'openid';

	public function before()
	{
		parent::before();

		// Load the OpenID library.
		require Kohana::find_file('vendor/openid', 'openid');

		// Create an OpenID consumer
		$this->openid = new LightOpenID($_SERVER['HTTP_HOST']);
	}

	public function action_begin()
	{
		$this->openid->identity = Kohana::$config->load('auth')->get('openid_identity');
		$this->openid->required = array('contact/email', 'namePerson');
		$this->redirect($this->openid->authUrl());
	}

	public function action_process()
	{
		// Validate the OpenID request.
		if ($this->openid->validate())
		{
			// Get the attributes from the request.
			// We need the email address to determine to associate with a person in the CMS database.
			$attrs = $this->openid->getAttributes();

			// Load the person with the given email address.
			$person = new Model_Person(array('email' => $attrs['contact/email']));

			// Does the person exist?
			if ( ! $person->loaded() || ! $person->enabled)
			{
				$this->_log_login_failure();
				// No, the given email address isn't allowed to login to this CMS.
				// TODO: Make a nice 'go away' page.
				throw new Exception('Invalid email');
			}

			// If the OpenID provider has been nice enough to tell us the person's name
			// And their name hasn't been set in the CMS database (it doesn't have to be given to add a person to the database)
			// Then set the person's name.
			if (isset($attrs['namePerson']) && $person->name == null)
			{
				// Update the person's name.
				$person
					->set('name', $attrs['namePerson'])
					->update();
			}

			$this->auth->force_login($person);
			Session::instance()->write();

			$this->_login_complete();
		}
		else
		{
			// OpenId request is invalid.
			// Redirect the user back here to start the login process again.
			$this->redirect('/cms/login/openid');
		}
	}
}