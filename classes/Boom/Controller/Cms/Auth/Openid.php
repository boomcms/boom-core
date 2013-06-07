<?php

class Boom_Controller_Cms_Auth_Openid extends Controller_Cms_Auth
{
	public $openid;

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
		// Set which OpenID provider to user.
		$this->openid->identity = Kohana::$config->load('openid')->get('identity');

		// Ask the provider nicely to give use the user's email address and name.
		$this->openid->required = array('contact/email', 'namePerson');

		// Redirect the user to to the OpenID provider.
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
			$this->auth->force_login($person);

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