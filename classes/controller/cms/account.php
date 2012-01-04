<?php

/**
* Controller to handle CMS account related actions - login, logout, reset password, etc.
* Most of the actual work is done by the Auth module but this controller adds extra stuff like logging and displaying the templates.
* Although this is part of the CMS, and part of the /cms controller directory it extends the site controller.
* This is because the cms controller requires a user to be logged in.
* Which would just be silly here.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms_Account extends Kohana_Controller
{	
	/**
	* Log a user into the CMS
	* @uses Auth::login()
	* @return void
	*/
	public function action_login() 
	{
		$protocol = $this->request->protocol();
		
		if (Auth::instance()->logged_in())
		{
			//You're already logged in dummy, just go away.
			$uri = '/';
			$cms_uri = Cookie::get( 'cms_uri' );
			
			if ($cms_uri != 'cms')
				$uri .= $cms_uri;

			$this->request->redirect( $uri );
		}
		
		// Gather form data.
		$email = Arr::get( $_POST, 'email', null );
		$password = Arr::get( $_POST, 'password', null );
		$persist = Arr::get( $_POST, 'persist', false );
		$return = array();
		$msg = '';
		
		if ($email && $password)
		{
			// A nice little touch to save Hoop people's fingers.
			// If there's no @ in the email address add @hoopassociates.co.uk to the end.
			if (!strstr( $email, '@' ))
				$email .= "@hoopassociates.co.uk";
				
			// Do this now and we can pass it to Auth::login() so we only have to query the database once.
			$person = ORM::factory('person')->where( 'emailaddress', '=', $email )->find();
			
			// $this->auth does the actual logging in, we just do some cleaning up after.
			if (Auth::instance()->login( $person, $password, $persist ))
			{				
				// Log the activity, so we can see what everyone's been getting up to.
				Model_Activitylog::log( $person, 'login' );

				// Where shall we send them next?
				$uri = '/';
				$cms_uri = Cookie::get( 'cms_uri' );
				
				if ($cms_uri != 'cms')
					$uri .= $cms_uri;

				$return['message'] = 'Login successful.';
				$return['outcome'] = 'success';
				$return['redirecturl'] = $uri;
			}
			else
			{
				$return['message'] = "We couldn't find your account. Please try again or <a class=\"resetpasswordlink\" href=\"/cms/account/forgotten\">click here</a> to reset your password.";
				$return['outcome'] = 'error';
			}
		}
		else
		{
			if ($email && !$password)
			{
				$return['message'] = "Please enter your password.";
				$return['outcome'] = 'error';
			}
			else if ($password && !$email)
			{
				$return['message'] = "Sorry, you gave us your password but we don't know who you are.";
				$return['outcome'] = 'error';
			}
		}
		
		//We've not given up already? Oh well, we'd best give them something to look at.
		if ($this->request->is_ajax())
		{
			echo json_encode( $return );
			exit();
		}
		else
		{
			// Login form
			$template = View::factory( 'cms/tpl_login' );
			$template->client = Kohana::$config->load('core')->get('client_name');
			if (isset( $return['message'] ))
				$template->msg = $return['message'];
			$template->email = $email;
			$template->tab = 'login';
			$template->persist = $persist;
			echo $template;
			exit();
		}
	}
	
	/**
	* Log the user out of the CMS
	*
	* @uses Auth::logout()
	* @return void
	*/
	public function action_logout()
	{
		if (Auth::instance()->logged_in())
		{
			Model_Activitylog::log( Auth::instance()->get_user(), 'logout' );
		
			Auth::instance()->logout(TRUE);
		}
		
		$this->request->redirect( '/' );
	}
	
	/**
	* Reset the user's CMS password, the muppet's forgotten it.
	*
	* @uses Text_Password
	* @return void
	*/
	public function action_forgotten()
	{
		$return = array();
		$email = Arr::get( $_POST, 'email' );

		if (!empty( $email ))
		{
			$person = ORM::factory( 'person' )->where( 'emailaddress', '=', $email )->find();
			
			if ($person->loaded())
			{	
				// Log that someone's done something
				Model_Activitylog::log( $person, 'password reset' );

				// Create a new password and update the user.
				// FYI Text_Password is a pear module.
				include 'Text/Password.php';
				$tp = new Text_Password();
				$passwd = $tp->create(8);
				
				$person->password =  '{SHA}' . base64_encode(sha1($passwd, true));
				$person->consecutive_failed_login_counter = 0;
				$person->save();
				
				// Send an email with the new password.
				$to = $person->emailaddress;
				$subject = Kohana::$config->load('config')->get('client_name') . ' CMS: Your password has been reset';
				$message = new View('cms/email/tpl_login_reset');
				$message->person = $person;
				$message->password = $passwd;
				
				$headers = 'From: hoopmaster@hoopassociates.co.uk' . "\r\n" .
							'Reply-To: mail@hoopassociates.co.uk' . "\r\n" ;
				mail($to, $subject, $message, $headers);

				$return['outcome'] = 'success';
				$return['message'] = 'Your password will be emailed to you shortly. If you do not receive it today, contact the Hoop team for assistance.';
			}
			else
			{
				$return['outcome'] = 'error';
				$return['message'] = "Sorry, we don't seem to know that one. Either try again or contact the hoop team for assistance.";
			}
		}
		
		if ($this->request->is_ajax())
		{
			echo json_encode( $return );
		}
		else
		{
			// Password reset form
			$template = View::factory( 'cms/tpl_login' );
			$template->email = $email;
			$template->client = Kohana::$config->load('core')->get('client_name');
		
			if (isset( $return['message'] ))
				$template->msg = $return['message'];
		
			$template->tab = 'reset';
			echo $template;
		}
		
		exit();
	}
}
