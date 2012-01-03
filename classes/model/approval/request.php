<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class Model_Approval_Request extends ORM_Versioned {
	protected $_table_name = 'approval_request';
	
	/**
	* Value for approval status new.
	* @var integer
	*/
	const STATUS_NEW = 1;

	/**
	* Value for approval status edited.
	* @var integer
	*/
	const STATUS_EDITED = 2;
	
	/**
	* Value for approval status rejected.
	* @var integer
	*/
	const STATUS_REJECTED = 3;
	
	/**
	* Value for approval status approved.
	* @var integer
	*/
	const STATUS_APPROVED = 4;
	
	/**
	* Value for approval status published.
	* @var integer
	*/
	const STATUS_PUBLISHED = 5;

	/**
	* Value for approval status deleted.
	* @var integer
	*/
	const STATUS_DELETED = 6;
	
	/**
	* Return a human readable representation of the approval status.
	*
	* @return string Approval status
	*/
	public function getApprovalStatus() {
		switch( $this->approval_status ) {
			case self::STATUS_NEW:
				return 'New';
				break;
			case self::STATUS_EDITED:
				return 'Edited';
				break;
			case self::STATUS_REJECTED:
				return 'Rejected';
				break;
			case self::STATUS_APPROVED:
				return 'Approved';
				break;
			case self::STATUS_PUBLISHED:
				return 'Published';
				break;
			case self::STATUS_DELETED:
				return 'Deleted';
				break;
			default:
				throw new Kohana_Exception( "Approval request has unknown request status: " . $this->approval_status );
		}	
	}		
}

?>