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
class Model_Person extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person';

	protected $_has_many = array( 
		'versions'			=> array( 'model' => 'version_person', 'foreign_key' => 'id' )
		//'sent_messages'		=> array( 'model' => 'message' ),
		//'received_messages'	=> array( 'model' => 'message' )
	);
	protected $_belongs_to = array( 'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'audit_person' ) );
	
	
	/**
	* Shortcut method for getting a user's full name.
	*
	* @return string <first name> <last name>
	*/
	public function getName()
	{
		return $this->version->firstname . " " . $this->version->lastname;
	}
	
}

?>