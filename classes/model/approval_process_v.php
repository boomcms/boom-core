<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class approval_process_v_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $belongs_to = array('approval_process' => array('model' => 'approval_process'));
	
	/**
	* Value for approval process action type 'send approval request'
	* @var integer
	*/
	const ACTION_TYPE_SEND = 1;
	
	/**
	* Value for approval process action type 'get approval'
	* @var integer
	*/
	const ACTION_TYPE_GET = 2;
	
	/**
	* Value for approval process action type 'publish page'
	* @var integer
	*/
	const ACTION_TYPE_PUBLISH = 3;
	
	/**
	* Value for approval target any
	* @var integer
	*/
	const TARGET_ANY = 1;
	
	/**
	* Value for approval target person
	* @var integer
	*/
	const TARGET_PERSON = 2;
	
	/**
	* Value for approval target group
	* @var integer
	*/
	const TARGET_GROUP = 3;	
	
	
	/**
	* Method to retrieve a human readable approval process action type.
	*
	* @return string Action type
	*/
	public function getActionType() {
		switch( $this->action_type ) {
			case self::ACTION_TYPE_SEND:
				return 'send approval request';
				break;
			case self::ACTION_TYPE_GET:
				return 'get approval';
				break;
			case self::ACTION_TYPE_PUBLISH:
				return 'publish page';
				break;
			default:
				throw new Kohana_Exception( 'Approval process has unknown action type value: ' . $this->action_type );
		}
	}
	
	/**
	* Method to retrieve a human readable approval target.
	*
	* @return string Approval target (any, person, or group)
	*/
	public function getApprovalTarget() {
		switch( $this->approval_target ) {
			case self::TARGET_ANY:
				return 'any';
				break;
			case self::TARGET_PERSON:
				return 'person';
				break;
			case self::TARGET_GROUP:
				return 'group';
				break;
			default:
				throw new Kohana_Exception( 'Approval process has unknown target value: ' . $this->approval_target );
		}
	}	
}