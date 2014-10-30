<?php

namespace Boom\Group;

use \DB as DB;

class Group
{
	/**
	 *
	 * @var \Model_Group
	 */
	protected $model;

	public function __construct(\Model_Group $model)
	{
		$this->model = $model;
	}

	public function getId()
	{
		return $this->model->id;
	}

	public function getName()
	{
		return $this->model->name;
	}

	public function getRoles($pageId = 0)
	{
		return DB::select('role_id', 'allowed')
			->from('group_roles')
			->where('group_id', '=', $this->getId())
			->where('page_id', '=', $pageId)
			->execute()
			->as_array('role_id', 'allowed');
	}

	/**
	 * Remove a role from a group.
	 *
	 * After removing the role from the group the permissions for the people the group are updated.
	 *
	 * @param integer $roleId
	 * @return \Boom\Group\Group
	 */
	public function removeRole($roleId)
	{
		$this->model->remove('roles', $roleId);

		// Remove the role from people in this group.
		DB::delete('people_roles')
			->where('group_id', '=', $this->getId())
			->where('role_id', '=', $roleId)
			->execute();

		return $this;
	}

	/**
	 *
	 * @return \Boom\Group\Group
	 */
	public function save()
	{
		$this->model->loaded()? $this->model->update() : $this->model->create();

		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @return \Boom\Group\Group
	 */
	public function setName($name)
	{
		$this->model->name = $name;

		return $this;
	}
}