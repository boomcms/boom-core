<?php

namespace Boom;

class Tag
{
	/**
	 *
	 * @var \Model_Tag
	 */
	protected $model;

	public function __construct(\Model_Tag $model)
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