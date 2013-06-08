<?php

class Boom_Controller_Cms_Auth_Logout extends Controller_Cms_Auth
{
	public function action_index()
	{
		// This needs to happen before we log the user out, or we don't be able to log who logged out.
		$this->_log_logout();

		$this->auth->logout(TRUE);

		// Send the user back to the homepage.
		$this->redirect('/');
	}
}