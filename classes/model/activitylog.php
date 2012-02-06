<?php

/**
* Logs actions performed in the CMS.
*
*
* Table name: activitylog
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	remotehost		****	string		****	The IP address the action was performed from. In the format aaa.bbb.ccc.ddd.
****	activity		****	string		****	What the user did.
****	note			****	string		****	Extra information relating to the action.
****	person			****	integer		****	The person ID of the user performing the action
****	time			****	integer		****	Unix timestamp of when the action occurred.
******************************************************************
*
* @package Activitylog
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Activitylog extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'activitylog';
	//protected $has_one = array( 'person' => array('model' => 'person'));
	//protected $foreign_key = array( 'person' => 'audit_person' );
	
	/**
	* Function to log an activity.
	* @example activitylog_Model::log( $remote, $person, $activity, $note);
	* @param string $remote The remote host of the user.
	* @param object $person person_Model representing the current user.
	* @param string $activity What they did.
	* @param string $note Additional note about the activity.
	* @return void
	*/
	public static function log( Model_Person $person, $activity, $note = null) {
		$log = new self;
		$log->remotehost = Request::$client_ip;
		$log->person = $person->id;
		$log->activity = $activity;
		$log->note = $note;
		$log->time = time();
		
		$log->save();		
	}
}

?>