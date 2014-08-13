<?php

namespace Boom;

use Boom\Group as Group;

class Person
{
	/**
	 *
	 * @var \Model_Person
	 */
	protected $model;

	public function __construct(\Model_Person $model)
	{
		$this->model = $model;
	}
	
	public function getEmail()
	{
		return $this->model->email;
	}

	public function getGroups()
	{
		$finder = new Group\Finder;

		return $finder
			->addFilter(new Group\Finder\Filter\Person($this))
			->findAll();
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