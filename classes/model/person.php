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
		'roles'		=> array( 
			'model'		=> 'role',
			'through'	=> 'person_role',
		),
		'activities' => array( 'model' => 'activitylog', 'foreign_key' => 'person' ),
	);	
	protected $_belongs_to = array( 
		'version'  => array( 'model' => 'version_person', 'foreign_key' => 'active_vid' ), 
	);	

	protected $_load_with = array( 'version' );
	
	/**
	* Stores the user's bitwise permissions after retrieval from the database.
	* @access private
	* @var array
	*/
	private $_permissions;
	
	/**
	* Get the user's CMS permissions
	* Permissions use bitwise comparison.
	* A user can have many permissions assigned to them from each of the roles they have.
	* This query uses the bit_or function to find each of the permissions which are set in one integer.
	*
	* @return int
	*/
	public function permissions( $where = 'cms' )
	{
		$key = (string) $where;
		
		if (!isset($this->_permissions[$key]))
		{		
			// What permissions does the user have at this level of the page tree?
			if ($where instanceof Page || $where instanceof Model_Page)
			{
				$query = DB::query( Database::SELECT, "select bit_or( permission ) as perm from person_role inner join permissions on person_role.role_id = permissions.role_id inner join page_mptt on permissions.where_id = page_mptt.page_id inner join actions on permissions.action_id = actions.id where where_type = 'page' and lft <= :lft and rgt >= :rgt and person_role.person_id = :person group by person_role.person_id" );

				$query->param( ':lft', $where->mptt->lft );
				$query->param( ':rgt', $where->mptt->rgt );
				$query->param( ':person', $this->id );
				$result = $query->execute();
					
				$this->_permissions[$key] = $result->get( 'perm' );
			}
			else if ( !$where )
			{	
				// Non-treed permissions.
				// Datbase query to retrieve the user's permissions combined across all assigned roles.
				$query = $this->_db->query( Database::SELECT, "select bit_or( permission ) as perm from person_role inner join role on role_id = role.id inner join role_v on role.active_vid = role_v.id where person_id = $this->id group by person_id" );
				if ($query->count() === 0)
					return 0;
			
				$this->_permissions[$key] = $query->get( 'perm' );	
			}
		}		
		
		return $this->_permissions[$key];
	}
	
	/**
	* Can the user perform the requested action?
	* This method can be used in a couple of ways
	* To get permissions for a CMS action which isn't part of a tree $person->can( 'manage people' )
	* To get permissions for an object which is in a tree $person->can( 'edit page', $page )
	*
	* @param string The requested action
	* @return bool true if they have the permission, false if not.
	* @see http://www.php4every1.com/tutorials/implementing-bitwise-permissions/#bw_actions-2
	*/
	public function can( $action, $where = false )
	{
		$action = ORM::factory( 'action' )->where( 'name', '=', $action )->find();
		
		// If the action doesn't exist just say no.
		if (!$action->loaded())
			return false;
		
		$perms = (int) $action->permission & $this->permissions( $where );
			
		return (bool) $perms;
	}
	
	/**
	* Does the object represent a user who can log in?
	* The name of this method is slightly misleading - it determines whether the person is a user, not whether they're actually logged in.
	* But it's used in place of Auth::logged_in() to allow for user mimicking
	* @todo Add to the Auth class as a way of implementing this better.
	* @return bool false if the person is unloaded or a guest. True if they're a user.
	*/
	public function logged_in()
	{
		if (!$this->loaded() || $this->emailaddress == 'guest@hoopassociates.co.uk' || $this->enabled == false)
			return false;
		else
			return true;		
	}
	
	public function complete_login()
	{
		$this->consecutive_failed_login_counter = 0;
		$this->save();
	}
}

?>