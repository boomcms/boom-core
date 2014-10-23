<?php

namespace Boom\Template;

class Finder extends \Boom\Finder
{
	public function __construct()
	{
		$this->_query = new \Boom\Model\Template;
	}

	public function find()
	{
		$model = parent::find();
		return new Template($model);
	}

	public function findAll()
	{
		$templates = parent::findAll()->as_array();

		return new \Boom\ArrayCallbackIterator($templates, function($template) {
			return new Template($template);
		});
	}
}