<?php

/**
*
* The version table for people.
*
* Table name: person_v
* This is stored in a different database!
* 
*************************** Table Columns ************************
****	Name							****	Data Type	****	Description		
****	id								****	integer		****	Primary key. auto increment.			
****	rid								****	integer		****	ID of the person that this version belongs to.
****	firstname						****	string		****	The person's first name.
****	lastname						****	string		****	The person's last name.
****	emailaddress					****	string		****	Unique field, usually the column which is used to find user details.
****	password						****	string		****	Hash password.
****	consecutive_failed_login_counter****	integer		****	Number of failed logins in a row. Used for locking an account after 5 failed logins. This should perhaps not be versioned?
****	enabled							****	boolean		****	Set to false to prevent the account being used.
****	audit_person					****	integer		****	Person ID of the user who created the version.
****	audit_time						****	integer		****	Unix timestamp of when the version was created.
****	deleted							****	boolean		****	Whether the group has been deleted.
****	image_id						****	integer		****	Asset ID of the user's profile image.
******************************************************************
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
	protected $_db_group = 'hoopid';
	
	protected $_has_one = array(
		'person'	=> array( 'model' => 'person', 'foreign_key' => 'id' ),
	);
	protected $_belongs_to = array(
		'image'	=>	array( 'model'	=> 'asset', 'foreign_key' => 'image_id' ),
	);
	
	/**
	* ORM Validation rules
	* @see http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	public function rules()
		{
		return array(
			'firstname' => array(
				array('not_empty'),
			),
			'lastname' => array(
				array('not_empty'),
			),
			'emailaddress' => array(
				array('not_empty'),
				array('email'),
			),
		);
	}
    	
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
		// Check that the password hasn't already been encrypted.
		// This is necessary because of the way table versioning works.
		// When a new version is created the current values are copied to a new Model_Version_Person object.
		// This can cause a password to be encrypted each time a new version is saved.
		if (substr( $password, 0, 5 ) != "{SHA}")
		{
			return "{SHA}" . base64_encode(sha1($password, TRUE));
		}
		else
		{
			return $password;
		}
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
			
			// We should also setup an email class which handles the templating and mailing.
			$subject = Kohana::$config->load('config')->get('client_name') . ' CMS: Account Locked';
			$message = View::factory('cms/email/tpl_account_locked');
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
