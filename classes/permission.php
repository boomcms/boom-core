<?php
/**
* This is the base class for the Permissions system.
* @package Permissions
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Permission
{
	/**
	* The person object which is having it's permissions checked.
	* @var object
	* @access protected
	*/
	protected $_person;
	
	/**
	* The query to check the user's permissions.
	* Some of this will be common to all types of permission.
	* So we generate the first bit in a shared constructor.
	*
	* @var object
	* @access protected
	*/
	protected $_query;
	
	/**
	* The database query result object.
	*
	* @param object
	*/
	protected $_result;
	
	/**
	* Permissions constructor
	* Create a permissions object to check for general permissions, i.e. not on a specific object
	*
	* @param Model_Person $person
	* @return Permissions object
	*/
	protected function __construct( Model_Person $person )
	{
		$this->_person = $person;	
		
		$this->_query = DB::select()->from( 'person_group' )
						->join( 'permissions', 'inner' )
						->on( 'person_group.group_id', '=', 'permissions.group_id' )
						->where( 'person_group.person_id', '=', $person->pk() );
	}
	
	/**
	* Permissions factory.
	*
	* @param Model_Person The person who's permissions we're checking
	* @param mixed The object we're checking for permissions on
	* @return Permissions Permissions object
	*/
	public static function factory( Model_Person $person, $object = false, $property = false )
	{
		if ($object instanceof Model_Page)
		{
			if ($property)
			{
				return new Permission_Page_Property( $person, $object, $property );
			}
			else
			{
				return new Permission_Page( $person, $object );
			}
		}
		else
		{
			return new Permission_Cms( $person, $object );
		}
	}
	
	/**
	* Find all matching permissions.
	* Could be useful for finding all permissions for a user, or all users who have permissions for an action.
	*/
	public function find_all()
	{
		$this->_query->select( "permissions.*" )
					->select( "actions.*" )
					->select( array(  'group_v.name', 'group_name' ) )
					->join( 'groups' )
					->on( 'groups.id', '=', 'person_group.group_id' )
					->join( 'group_v' )
					->on( 'groups.active_vid', '=', 'group_v.id' );
		$result = $this->_query->execute();
		
		return $result;
	}
	
	/**
	* Check the permissions for a specific action
	*
	* @param string The desired action
	* @param int
	* @return boolean True if the person can, false if they can't
	*/
	public function can( $action )
	{
		$this->_result = $this->_query->select( "permissions.permission" )->execute();
	}
}

?>