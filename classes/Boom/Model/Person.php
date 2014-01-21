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

	/**
	 *
	 * @param integer $group_id
	 * @return Model_Person
	 */
	public function by_group($group_id)
	{
		if ($group_id)
		{
			$this
				->join('people_groups', 'inner')
				->on('person_id', '=', 'id')
				->where('group_id', '=', $group_id);
		}

		return $this;
	}

	public function complete_login()
	{
		return $this
			->set('failed_logins', 0)
			->set('locked_until', 0)
			->update();
	}

	public function filters()
	{
		return array(
			'email' => array(
				array('strtolower'),
			)
		);
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