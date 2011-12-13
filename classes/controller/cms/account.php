<?php

/**
* Class to handle CMS account related actions - login, logout, reset password, etc.
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Account_Controller extends Controller_Template {	
	/**
	* Log a user into the CMS
	* @uses Auth::login()
	* @return void
	*/
	public function action_login() {
		$protocol = ($this->input->server( 'https' ))? "https://" : "http://";
		
		if ($this->auth->logged_in()) {
			//You're already logged in dummy, just go away.
			$uri = '/';
				
			if ($this->input->cookie( 'cms_uri' ) != 'cms')
				$uri .= $this->input->cookie( 'cms_uri' );

			url::redirect($protocol . $this->input->server( 'SERVER_NAME' ) . $uri, 301);
			exit;
		}
		
		// Gather form data.
		$email = $this->input->post( 'email', null, true );
		$password = $this->input->post( 'password', null );
		$persist = $this->input->post( 'persist', false );
		$msg = '';
		
		if ($email && $password) {
			// A nice little touch to save Hoop people's fingers.
			// If there's no @ in the email address add @hoopassociates.co.uk to the end.
			if (!strstr( $email, '@' ))
				$email .= "@hoopassociates.co.uk";
				
			// Do this now and we can pass it to Auth::login() so we only have to query the database once.
			$person = O::fa('person')->find_by_emailaddress( $email );
			
			// $this->auth does the actual logging in, we just do some cleaning up after.
			if ($this->auth->login( $person, $password, false, $persist )) {			
				// We can cache the user details here for subsequent requests.			
				$cache = Cache::Instance();
				$cache->set( 'user_person_' . $email, $person, 'user_setting');
				
				// Log the activity, so we can see what everyone's been getting up to.
				Model_Activitylog::log( $this->request->client_ip, $this->person, 'login' );

				// Where shall we send them next?
				$uri = '/';
				
				if ($this->input->cookie( 'cms_uri' ) != 'cms')
					$uri .= $this->input->cookie( 'cms_uri' );

				// Be gone with you.
				url::redirect($protocol . $this->input->server( 'SERVER_NAME' ) . $uri, 301);
				exit;
			} else
				$msg = "We couldn't find your account.	Please try again or <a class=\"resetpasswordlink\" href=\"/cms/forgot-password\">click here</a> to reset your password.";
		} else {
			if ($email && !$password)
				$msg = "Please enter your password.";
			else if ($password && !$email)
				$msg = "Sorry, you gave us your password but we don't know who you are.";
		}
		
		//We've not given up already? Oh well, best show them a template I guess.
		// Main template.
		$v = new View( 'cms/standard_template');
		$v->set_global( 'title', $this->page->title );
		// Login form
		$v->subtpl = new View( 'cms/templates/tpl_login' );
		$v->subtpl->msg = $msg;
		$v->subtpl->email = $email;
		$v->subtpl->persist = $persist;
		// Some other template which was embedded. Not sure what it does.
		$v->subtpl->subtpl_cms_login_warning = new View('cms/subtpl_cms_login_warning');
		
		$v->render( true );	
	}
	
	/**
	* Log the user out of the CMS
	*
	* @uses Auth::logout()
	* @return void
	*/
	public function action_logout() {
		$cp = O::f('activitylog_v');
		$cp->remotehost = $this->input->ip_address();
		$cp->activity = "logout";
		$cp->save_activeversion();

		$this->auth->logout(TRUE);
		cookie::delete('kohanasession_data');

		if (Kohana::config('core.force_ssl'))
			url::redirect("https://" . $this->input->server( 'SERVER_NAME' ) . "/", 301);
		else
			url::redirect("http://" . $this->input->server( 'SERVER_NAME' ) . "/", 301);
	}
	
	/**
	* Reset the user's CMS password, the muppet's forgotten it.
	*
	* @uses Text_Password
	* @return void
	*/
	public function action_reset() {
		$email = $this->input->post( 'email', null, true );
		$client = $this->input->get( 'client', 'Default client name', true );
		$msg = '';
		$cache = Cache::Instance();

		if (!empty($_POST)) {
			// find the user with this email address, return an error if we can't find them, make sure we do the search case insensitively

			if (!$person = $cache->get( 'user_person_by_email_' . $email ))
				$person = O::fa('person')->find_by_emailaddress(strtolower( $email ));
			
			if ($person->emailaddress) {	
				// Log that someone's done something
				Model_Activitylog::log( $this->request->client_ip, $this->person, 'password reset' );

				// Create a new password and update the user.
				include 'Text/Password.php';
				$tp = new Text_Password();
				$passwd = $tp->create(8);
				$person->password =  '{SHA}' . base64_encode(sha1($passwd, true));
				$person->consecutive_failed_login_counter = 0;
				$person->save_activeversion();
				$to = $person->emailaddress;
				$subject = Kohana::config('core.clientnamelong') . ' CMS: Your password has been reset';
				$message = new View('cms/email/tpl_login_reset');
				$headers = 'From: hoopmaster@hoopassociates.co.uk' . "\r\n" .
							'Reply-To: mail@hoopassociates.co.uk' . "\r\n" ;
				mail($to, $subject, $message, $headers);
				
				// Make sure we update the cache.
				$cache->set( 'user_person_by_email_' . $email, $person, 'user_setting' );
				$cache->set( 'user_person_by_rid_' . $person->rid, $person, 'user_setting' );

				$msg = 'Your password will be emailed to you shortly.  If you do not receive it today, contact the hoop team for assistance.';
				$this->passwordresetflag = 1;
			} else
				$msg = "Sorry, we don't seem to know that one.	Either try again or contact the hoop team for assistance.";
		}
		
		// Main template.
		$v = new View( 'cms/standard_template');
		$v->set_global( 'title', 'Password reset' );
		// Password reset subtemplate
		$v->subtpl = new View( 'cms/templates/tpl_password' );
		$v->subtpl->msg = $msg;
		$v->subtpl->email = $email;
		
		$v->render( true );
	}
}
