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
	const LOCK_WAIT = 600;

	protected $_table_name = 'people';

	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'email'		=>	'',
		'enabled'		=>	'',
		'password'		=>	'',
		'failed_logins'	=>	'',
		'locked_until'	=>	'',
		'avatar_id'	=>	'',
	);

	protected $_belongs_to = array(
		'avatar' => array('model' => 'Asset', 'foreign_key' => 'avatar_id'),
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
		'auth_logs' => array('model' => 'AuthLog'),
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
				DB::select(DB::expr($this->id), DB::expr($group_id), 'role_id', 'allowed', 'page_id')
					->from('group_roles')
					->where('group_id', '=', $group_id)
				)
			->execute($this->_db);

		return $this;
	}

	public function complete_login()
	{
		return $this
			->set('failed_logins', 0)
			->set('locked_until', 0)
			->update();
	}

	public function get_avatar()
	{
		return $this->avatar;
	}

	public function get_icon_url($s = 16)
	{
		if ($this->avatar_id)
		{
			return Route::url('asset', array('id' => $this->avatar_id, 'width' => $s, 'height' => $s));
		}
		else
		{
			return URL::gravatar($this->email, array('s' => $s));
		}
	}

	public function get_lock_wait()
	{
		if ($this->is_locked())
		{
			return Date::span($this->locked_until);
		}
	}

	public function get_recent_account_activity()
	{
		return $this
			->auth_logs
			->order_by('time', 'desc')
			->limit(10)
			->find_all();
	}

	public function is_locked()
	{
		return $this->locked_until AND ($this->locked_until > $_SERVER['REQUEST_TIME']);
	}

	/**
	 * Returns whether the current person is allowed to perform the specified role.
	 *
	 * A role can be given as a name or a Model_Role model.
	 * It's generally quicker to call this function with a role name than to load a role model (lol) and then call the function with the model.
	 *
	 * @param mixed $role
	 * @param Model_Page $page
	 *
	 * @return boolean
	 */
	public function is_allowed($role, Model_Page $page = NULL)
	{
		$query = DB::select(array(DB::expr("bit_and(allowed)"), 'allowed'))
			->from('people_roles')
			->where('person_id', '=', $this->id);

		// If the given role is a model then filter by role ID.
		// Otherwise join the roles table and query by role name.

		if (is_object($role))
		{
			$query->where('role_id', '=', $role->id);
		}
		else
		{
			$query
				->join('roles', 'inner')
				->on('people_roles.role_id', '=', 'roles.id')
				->where('roles.name', '=', $role);
		}

		$query->group_by('person_id');	// Strange results if this isn't here.

		if ($page !== NULL)
		{
			$query
				->join('page_mptt', 'left')
				->on('people_roles.page_id', '=', 'page_mptt.id')
				->where('lft', '<=', $page->mptt->lft)
				->where('rgt', '>=', $page->mptt->rgt)
				->where('scope', '=', $page->mptt->scope);
		}
		else
		{
			$query->where('people_roles.page_id', '=', 0);
		}

		$result = $query
			->execute()
			->as_array();

		return  ( ! empty($result) AND (boolean) $result[0]['allowed']);
	}

	public function login_failed()
	{
		$this->set('failed_logins', ++$this->failed_logins);

		if ($this->failed_logins > 3)
		{
			$this->set('locked_until', $_SERVER['REQUEST_TIME'] + static::LOCK_WAIT);
		}

		return $this->update();
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
		// Remove the relationship with the group.
		$this->remove('groups', $group_id);

		// Remove the permissions which were given by this group.
		DB::delete('people_roles')
			->where('group_id', '=', $group_id)
			->where('person_id', '=', $this->id)
			->execute($this->_db);

		// Return the person model.
		return $this;
	}
}