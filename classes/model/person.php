<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Person tagging.
* @todo Make the stuff copy and pasted from the Person library look nice.
* @todo Finish saving - including handling versioning. i.e. when we save create a new version. This is going to be a common problem across our versioned models.
* 
*/
class Model_Person extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person';
	protected $_has_one = array( 
		'version'	=> array( 'model' => 'version_person', 'foreign_key' => 'id' )
	);
	protected $_has_many = array( 
		'versions'			=> array( 'model' => 'version_person', 'foreign_key' => 'id' )
		//'sent_messages'		=> array( 'model' => 'message' ),
		//'received_messages'	=> array( 'model' => 'message' )
	);
	protected $_belongs_to = array( 'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'audit_person' ) );
	protected $_load_with = array( 'version' );
	
	/**
	* Set the user's password.
	*
	* @param string $text_password Plain text password which will be encrypted and set as the user's password.
	* @return void
	*/
	public function setPassword( $text_password )
	{
		$this->current_version->password = '{SHA}' . base64_encode(sha1($_POST['password'],true));
		
	}
	
	/**
	* Set the user's email address. Ensures that email addresses are lowercase.
	*
	* @param string $emailaddress
	* @return void
	*/
	public function setEmailAddress( $emailaddress )
	{
		$this->emailaddress = strtolower( $this->emailaddress );


	}
	
	
	/**
	* Shortcut method for getting a user's full name.
	*
	* @return string <first name> <last name>
	*/
	public function getName()
	{
		return $this->version->firstname . " " . $this->version->lastname;
	}
	
	/**
	* Determine whether the person is a Hoop user.
	*
	* @return boolean True if they're Hoop, false if a guest or some other user.
	*/
	public function isHoop()
	{
		return true;
	}
	
	public function save( Validation $validation = NULL )
	{

		
	}
	
}

?>