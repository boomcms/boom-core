<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Model_Person extends ORM
{
	protected $_table_name = 'people';

	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'email'		=>	'',
		'enabled'		=>	'',
		'theme'		=>	'',
	);

	/**
	 * Properties to create relationships with Kohana's ORM
	 */
	protected $_has_many = array(
		'groups'		=> array(
			'model'	=> 'Group',
			'through'	=> 'people_groups',
		),
		'logs' => array(),
	);

	/**
	 * Put the current person in a group.
	 *
	 * When a person is added to a group:
	 *
	 * * A relationship between the person and the group is created in the person_groups table.
	 * * The person obtains records for all the roles which the group has in the person_roles table.
	 *
	 *
	 * @param integer $group_id
	 * @return \Boom_Model_Person
	 */
	public function add_group($group_id)
	{
		// Create a relationship with the group.
		$this->add('groups', $group_id);

		// Inherit any roles assigned to the group.
		DB::insert('people_roles', array('person_id', 'group_id', 'role_id', 'allowed', 'page_id'))
			->select(
				DB::select(DB::expr($this->id), DB::expr('group_id'), 'role_id', 'allowed', 'page_id')
					->from('group_roles')
					->where('group_id', '=', $group_id)
				)
			->execute($this->_db);

		return $this;
	}

	/**
	 * Returns whether the current person is allowed to perform the specified role.
	 *
	 * @param Model_Role $role
	 * @param Model_Page $page
	 *
	 * @return boolean
	 */
	public function is_allowed(Model_Role $role, Model_Page $page = NULL)
	{
		$query = DB::select('allowed')
			->from('people_roles')
			->where('person_id', '=', $this->id)
			->where('role_id', '=', $role->id);

		if ($page !== NULL)
		{
			$query
				->join('page_mptt', 'left')
				->on('people_roles.page_id', '=', 'page_mptt.id')
				->and_where_open()
					->where('lft', '<=', $page->mptt->lft)
					->where('rgt', '>=', $page->mptt->rgt)
					->where('scope', '=', $page->mptt->scope)
					->or_where_open()
						->where('people_roles.page_id', '=', 0)
					->or_where_close()
				->and_where_close();
		}

		$result = $query
			->execute()
			->as_array();

		return  ( ! empty($result) AND (boolean) $result[0]['allowed']);
	}

	/**
	 * Removes a person from a group.
	 *
	 * When a person is removed from a group the person's roles are updated in the following ways:
	 *
	 *  * Any roles which are disallowed by the group but which have been allowed by another group which the person is a member of will become allowed.
	 *  * Any roles which the group allows which haven't been allowed by any other groups which the person is a member of will be removed from the person.
	 *
	 *
	 * @param integer $group_id
	 * @return \Boom_Model_Person
	 */
	public function remove_group($group_id)
	{
		return $this;
	}
}