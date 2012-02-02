<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Person extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person';
	protected $_db_group = 'hoopid';
	protected $_has_many = array( 
		'groups'		=> array( 
			'model'		=> 'group',
			'through'	=> 'person_group',
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
	* Assign the person to a group.
	* This can't be done using Kohana's $person->add because the tables are in different databases so it doesn't seem to work
	*
	* @param mixed $group Group ID or group object.
	* @return void
	*/
	public function add_group( $group )
	{
		if (!is_object( $group ))
		{
			// Even though we only need the group ID we load the group to check that it exists.
			$group = ORM::factory( 'group', $group );
		}
				
		if ($group instanceof Model_Group && $group->loaded())
		{
			ORM::factory( 'person_group' )
				->values( array( 'group_id' => $group->pk(), 'person_id' => $this->pk() ))
				->create();
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
	* @see http://www.php4every1.com/tutorials/implementing-bitwise-permissions/#bw_actions-2
	*/
	public function can( $action, $what = false, $property = false )
	{
		// If a record wasn't loaded, thye can't do it.
		if (!$this->loaded())
			return false;
			
		$permission = Permission::factory( $this, $what, $property );
		return $permission->can( $action );
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
