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
	 * Adds a role to the current group.
	 *
	 * This will also add the role to all members of the group.
	 *
	 *
	 * @param integer $role_id	ID of the role to add
	 * @param integer $allowed	Whether the group is allowed or prevented from the role.
	 * @param integer $page_id	Make the role active at a particular point in the page tree.
	 *
	 * @throws InvalidArgumentException When called with a role ID which is not in use.
	 *
	 * @return \Boom_Model_Group
	 */
	public function add_role($role_id, $allowed, $page_id = 0)
	{
		// Check that the role is exists.
		// Attempt to load the role from the database.
		$role = ORM::factory('Role', $role_id);

		// If the role wasn't found then it's not a valid role ID.
		if ( ! $role->loaded())
		{
			// Throw an exception.
			throw new InvalidArgumentException("Argument 1 to ".__CLASS__."::".__METHOD__." must be a valid role ID. Called with $role_id which doesn't exist.");
		}

		// Check that the group doesn't already have this role before continuing.
		if ( ! $this->has_role($role_id, $page_id))
		{
			// Create a relationship with the role.
			// Don't use [ORM::add()] because we want to store the value of $allowed as well.
			DB::insert('group_roles', array('group_id', 'role_id', 'allowed', 'page_id'))
				->values(array($this->id, $role_id, $allowed, $page_id))
				->execute($this->_db);

			// If the page ID is isn't set the set it to a string with '0' as the contents
			// otherwise it won't be included in the DB::select()
			if ($page_id)
			{
				DB::insert('people_roles', array('person_id', 'group_id', 'role_id', 'allowed', 'page_id'))
					->select(
						DB::select('person_id', 'group_id', DB::expr($role_id), DB::expr($allowed), DB::expr($page_id))
							->from('people_groups')
							->where('group_id', '=', $this->id)
					)
					->execute($this->_db);
			}
			else
			{
				DB::insert('people_roles', array('person_id', 'group_id', 'role_id', 'allowed'))
					->select(
						DB::select('person_id', 'group_id', DB::expr($role_id), DB::expr($allowed))
							->from('people_groups')
							->where('group_id', '=', $this->id)
					)
					->execute($this->_db);
			}
		}

		return $this;
	}

	/**
	 * Delete a group.
	 *
	 * Groups are not really deleted.
	 * Instead the 'deleted' column is set to true.
	 * When marked as deleted a group will not appear in group lists and it's permissions will not effect the permissions of the people who belong to the group.
	 *
	 * We mark groups as deleted rather than deleting them completely so that mistakes can be reverted easily.
	 * If we actually deleted a group we'd lose the records of:
	 *
	 * * Which people belong to the group.
	 * * Which roles have been assigned to the group.
	 *
	 * This could cause a big headache if an important group was mistakenly deleted.
	 *
	 * @return \Boom_Model_Group
	 */
	public function delete()
	{
		// Delete the people_roles records for this group
		// so that this group doesn't influence people's permissions.
		DB::delete('people_roles')
			->where('group_id', '=', $this->id)
			->execute($this->_db);

		// Make the group as deleted.
		$this->deleted = true;

		// Save the changes.
		$this->update();

		// Return a cleared group model.
		return $this->clear();
	}

	/**
	 * Determines whether the current group has a specified role allowed / denied.
	 *
	 * ORM::has() doesn't work for this since we also have to take into account the page ID, if one is given.
	 *
	 * @param integer $role_id
	 * @param integer $page_id
	 * @return boolean
	 */
	public function has_role($role_id, $page_id = 0)
	{
		// If the group isn't loaded then it can't have the role.
		if ( ! $this->_loaded)
		{
			return false;
		}

		$result = DB::select(DB::expr(1))
			->from('group_roles')
			->where('group_id', '=', $this->id)
			->where('role_id', '=', $role_id)
			->where('page_id', '=', $page_id)
			->execute($this->_db)
			->as_array();

		// The group has the role if a result was returned.
		return (count($result) > 0);
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
	public function names($exclude = null)
	{
		// $exclude should be an array or DB select.
		if ($exclude !== null && ! (is_array($exclude) || $exclude instanceof Database_Query_Builder_Select))
		{
			// Throw an exception.
			throw new InvalidArgumentException("Argument 1 for ".__CLASS__."::".__METHOD__." should be an array or instance of Database_Query_Builder_Select, ".tyepof($excluding). "given");
		}

		// Prepare the query
		$query = DB::select('id', 'name')
			->from($this->_table_name)
			->where('deleted', '=', false)
			->order_by('name', 'asc');

		// Are we excluding any groups?
		if ($exclude !== null)
		{
			// Exclude these groups from the results.
			$query->where('id', 'NOT IN', $exclude);
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
		// Remove the relationship between this group and the role.
		$this->remove('roles', $role_id);

		// Remove the role from people in this group.
		DB::delete('people_roles')
			->where('group_id', '=', $this->id)
			->where('role_id', '=', $role_id)
			->execute($this->_db);

		return $this;
	}

	public function roles($page_id = 0)
	{
		// Check that the given page ID is an integer.
		if ( ! is_int($page_id))
		{
			// No it's not, leave us alone.
			throw new InvalidArgumentException('Argument 1 for '.__CLASS__.'::'.__METHOD__.' must be an integer, '.gettype($page_id).' given');
		}

		// Run the query and return the results.
		return DB::select('role_id', 'allowed')
			->from('group_roles')
			->where('group_id', '=', $this->id)
			->where('page_id', '=', $page_id)
			->execute($this->_db)
			->as_array('role_id', 'allowed');
	}

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
}