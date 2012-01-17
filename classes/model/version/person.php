<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Version_Person extends Model_Version {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person_v';
	protected $_has_one = array(
		'person'	=> array( 'model' => 'person', 'foreign_key' => 'id' ),
	);
	protected $_belongs_to = array(
		'image'	=>	array( 'model'	=> 'asset', 'foreign_key' => 'image_id' ),
	);
	
	/**
	* Filters for the versioned person columns
	* @see http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'emailaddress' => array(
				array( 'strtolower' ),
			),
	        'password' => array(
	            array(array($this, 'hash_password')),
	        ),
			'consecutive_failed_login_counter' => array(
				array(array($this, 'login_failed')),
			),
	    );
	}
	
	/**
	* Encrypts the password.
	*
	* @param string $password Plaintext password
	* @return string The hashed password
	*/
	protected function hash_password( $password )
	{
		return "{SHA}" . base64_encode(sha1($password, TRUE));
	}
	
	/**
	* Determines whether the user has the given password.
	*
	* @param string $password The password to be checked.
	* @return bool True if the passwords match, false if not
	*/
	public function has_password( $password )
	{
		return $this->hash_password( $password ) == $this->password;
	}
	
	/**
	* User has failed to login.
	* This is run as a filter when the consecutive_failed_login_counter is set.
	* 
	* @param int $failed_counter The new value of the failed login counter.
	*/
	public function login_failed( $failed_counter )
	{	
		if ($this->consecutive_failed_login_counter >= 5)
		{
			$this->enabled = false;
			
			// I don't really like this code being here.
			// It could perhaps become a filter in the Person model when the failed counter is set?
			// We should also setup an email class which handles the templating and mailing.
			$subject = Kohana::$config->load('config')->get('client_name') . ' CMS: Account Locked';
			$message = new View('cms/email/tpl_account_locked');
			$message->person = $this;
		
			$headers = 'From: hoopmaster@hoopassociates.co.uk' . "\r\n" .
						'Reply-To: mail@hoopassociates.co.uk' . "\r\n" ;
			mail($this->emailaddress, $subject, $message, $headers);
		}
		
		return $failed_counter;
	}
	
	/**
	* Shortcut method for getting a user's full name.
	*
	* @return string <first name> <last name>
	*/
	public function getName()
	{
		return $this->firstname . " " . $this->lastname;
	}
}

?>
