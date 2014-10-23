<?php

namespace Boom;

use \Kohana as Kohana;
use \Model_Template as Model_Template;
use \View as View;

class Template
{
	const DIRECTORY = 'site/templates/';

	/**
	 *
	 * @var Model_Template
	 */
	protected $model;

	public function __construct(\Model_Template $model)
	{
		$this->model = $model;
	}

	public function countPages()
	{
		if ( ! $this->model->loaded()) {
			return 0;
		}

		$finder = new Page\Finder;
		$finder->addFilter(new Page\Finder\Filter\Template($this));
		return $finder->count();
	}

	public function fileExists()
	{
		return (bool) Kohana::find_file("views", $this->getFullFilename());
	}

	public function getControllerName()
	{
		$parts = explode('_', $this->model->filename);

		foreach ($parts as & $part) {
			$part = ucfirst($part);
		}

		return implode('_', $parts);
	}

	public function getDescription()
	{
		return $this->model->description;
	}

	public function getFilename()
	{
		return $this->model->filename;
	}

	public function getFullFilename()
	{
		return static::DIRECTORY.$this->getFilename();
	}

	public function getId()
	{
		return $this->model->id;
	}

	public function getName()
	{
		return $this->model->name;
	}

	public function getView()
	{
		return new View($this->getFullFilename());
	}
}