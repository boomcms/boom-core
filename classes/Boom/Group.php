<?php

namespace Boom;

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
}