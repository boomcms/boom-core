<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Group extends ORM
{
	protected $_has_many = array('roles' => array('through' => 'group_roles'));

	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'deleted'		=>	'',
	);

	/**
	 * ORM Validation rules
	 * @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	 */
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
			),
		);
	}

	/**
	 * Adds a role to the current group.
	 *
	 * This will also add the role to all members of the group.
	 *
	 *
	 * @param integer $role_id	ID of the role to add
	 * @param integer $allowed	Whether the group is allowed or prevented from the role.
	 * @param integer $page_id	Make the role active at a particular point in the page tree.
	 *
	 * @return \Boom_Model_Group
	 *
	 * @todo Implement
	 */
	public function add_role($role_id, $allowed, $page_id = NULL)
	{
		// Check that the group doesn't already have the role.
		$existing = ORM::factory('Group_Role', array(
			'group_id'	=>	$this->id,
			'role_id'	=>	$role_id,
			'page_id'	=>	$page_id,
		));

		// Code taken from controller:
//				DB::insert('group_roles', array('group_id', 'role_id', 'page_id', 'allowed'))
//					->values(array(
//						$this->group->id,
//						$role_id,
//						$page_id,
//						$enabled
//					))
//					->execute();
//
//				// Update the permissions for the people in this group.
//				DB::insert('people_roles', array('person_id', 'group_id', 'role_id', 'page_id', 'allowed'))
//					->select(
//						DB::select('person_id', DB::expr($this->group->id), DB::expr($role_id), DB::expr($page_id), DB::expr($enabled))
//							->from('people_groups')
//							->where('group_id', '=', $this->group->id)
//					)
//					->execute();

		return $this;
	}
}