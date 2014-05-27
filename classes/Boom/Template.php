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

	public function __construct(Model_Template $model)
	{
		$this->model = $model;
	}

	public function countPages()
	{
		if ( ! $this->model->loaded()) {
			return 0;
		}

		$finder = new Finder\Page;
		$finder->addFilter(new Finder\Page\Filter\Template($this->model));
		return $finder->count();
	}

	public function fileExists()
	{
		return (bool) Kohana::find_file("views", $this->getFilename());
	}

	public function getControllerName()
	{
		$parts = explode('_', $this->model->filename);

		foreach ($parts as & $part) {
			$part = ucfirst($part);
		}

		return implode('_', $parts);
	}

	public function getFilename()
	{
		return static::DIRECTORY.$this->model->filename;
	}

	public function getView()
	{
		return new View($this->getFilename());
	}
}