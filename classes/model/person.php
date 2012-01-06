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
		'versions'			=> array( 'model' => 'version_person', 'foreign_key' => 'id' ),
		//'sent_messages'		=> array( 'model' => 'message' ),
		//'received_messages'	=> array( 'model' => 'message' )
	);
	
	protected $_belongs_to = array( 'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'audit_person' ) );
	
	/**
	* Stores the user's bitwise permissions after retrieval from the database.
	* @access private
	* @var int
	*/
	private $_permissions;
	
	/**
	* Get the user's CMS permissions
	* Permissions use bitwise comparison.
	* A user can have many permissions assigned to them from each of the roles they have.
	* This query uses the bit_or function to find each of the permissions which are set in one integer.
	*
	* @todo Create an equivalent of $_permission for page permissions to stop repeated database queries for the same page.
	* @return int
	*/
	public function permissions( $where = false )
	{
		// What permissions does the user have at this level of the page tree?
		if ($where instanceof Page)
		{
			$query = DB::query( Database::SELECT, "select bit_or( permission ) as perm from person_role inner join permissions on person_role.role_id = permissions.role_id inner join page_mptt on permissions.where_id = page_mptt.page_id inner join actions on permissions.action_id = actions.id where where_type = 'page' and lft >= :lft and rgt <= :rgt group by permissions.role_id" );

			$query->param( ':lft', $where->mptt->lft );
			$query->param( ':rgt', $where->mptt->rgt );
			$result = $query->execute();
						
			return $result->get( 'perm' );	
		}
		else if ( !$where )
		{	
			// Non-treed permissions.
			if ($this->_permissions === null)
			{
				// Datbase query to retrieve the user's permissions combined across all assigned roles.
				$query = $this->_db->query( Database::SELECT, "select bit_or( permission ) as perm from person_role inner join role on role_id = role.id inner join role_v on role.active_vid = role_v.id where person_id = $this->id group by person_id" );
				if ($query->count() === 0)
					return 0;
				
				$this->_permissions = $query->get( 'perm' );		
			}		
		
			return $this->_permissions;
		}
	}
	
	/**
	* Can the user perform the requested action?
	* This method can be used in a couple of ways
	* To get permissions for a CMS action which isn't part of a tree $person->can( 'manage people' )
	* To get permissions for an object which is in a tree $person->can( 'edit page', $page )
	*
	* @param string The requested action
	* @return bool true if they have the permission, false if not.
	*/
	public function can( $action, $where = false )
	{
		$action = ORM::factory( 'action' )->where( 'name', '=', $action )->find();
		
		// If the action doesn't exist just say no.
		if (!$action->loaded())
			return false;
			
		return (int) $action->permission & $this->permissions( $where );
	}
}

?>