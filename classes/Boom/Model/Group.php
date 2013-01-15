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

	protected $_table_name = 'groups';

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

		return $this;
	}

	/**
	 * Returns an array of the ID and name of all groups.
	 * The returned array is sorted alphabetically by name, A - Z.
	 *
	 * This function can be used to build a select box of groups, e.g.:
	 *
	 *	<?= Form::select('group_id', ORM::factory('Group')->names()) ?>
	 *
	 *
	 * Optionally an array of group names, or a Database_Query_Builder_Select object, can be given to exclude those groups from the results.
	 * This could be used to get the names of all groups that a person is not already a member of.
	 *
	 *	<?= Form::select('group_id', ORM::factory('Group')->names(array('Group name'))) ?>
	 *
	 *
	 * @param mixed $exclude
	 * @return array
	 *
	 * @throws InvalidArgumentException
	 */
	public function names($exclude = NULL)
	{
		// $exclude should be an array or DB select.
		if ($exclude !== NULL AND ! (is_array($exclude) OR $exclude instanceof Database_Query_Builder_Select))
		{
			// Throw an exception.
			throw new InvalidArgumentException("Argument 1 for ".__CLASS__."::".__METHOD__." should be an array or instance of Database_Query_Builder_Select, ".tyepof($excluding). "given");
		}

		// Prepare the query
		$query = DB::select('id', 'name')
			->from($this->_table_name)
			->order_by('name', 'asc');

		// Are we excluding any groups?
		if ($exclude !== NULL)
		{
			// Exclude these groups from the results.
			$query->where('name', 'NOT IN', $exclude);
		}

		// Run the query and return the results.
		return $query
			->execute($this->_db)
			->as_array('id', 'name');
	}

	/**
	 * Remove a role from a group.
	 *
	 * After removing the role from the group the permissions for the people the group are updated.
	 *
	 * @param integer $role_id
	 * @return \Boom_Model_Group
	 */
	public function remove_role($role_id)
	{
		return $this;
	}
}