<?php

/**
*
* @package Message
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class message_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $has_one = array( 'version' => array('model' => 'message_v' ));
	protected $has_many = array( 
		'versions'			=> array('model' => 'message_v'),
	);
	protected $load_with = array( 'version' );
	protected $foreign_key = array( 'version' => 'active_vid' );	
	
	/**
	* Message status 'new' value.
	* @var integer
	*/
	const STATUS_NEW = 1;

	/**
	* Message status 'read' value.
	* @var integer
	*/
	const STATUS_READ = 2;
	
	/**
	* Message status 'deleted' value.
	* @var integer
	*/
	const STATUS_DELETED = 3;	
	
	/**
	* Method to show human readable status.
	*
	* @return string Message status (currently new, read, or deleted)
	*/
	public function getMessageStatus() {
		switch( $this->message_status ) {
			case self::STATUS_NEW:
				return 'New';
				break;
			case self::STATUS_READ:
				return 'Read';
				break;
			case self::STATUS_DELETED:
				return 'Deleted';
				break;
			default:
				throw new Kohana_Exception( 'Message has unknown message status value: ' . $this->message_status );
		}
	}
}

?>