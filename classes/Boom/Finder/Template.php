<?php

namespace Boom\Finder;

class Template extends \Boom\Finder
{
	public function __construct()
	{
		$this->_query = new \Boom\Model\Template;
	}

	public function find()
	{
		$model = parent::find();
		return new \Boom\Template($model);
	}

	public function findAll()
	{
		$templates = parent::findAll();
		return new Template\Result($templates);
	}
}