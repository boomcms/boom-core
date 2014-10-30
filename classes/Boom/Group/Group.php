<?php

namespace Boom\Group;

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