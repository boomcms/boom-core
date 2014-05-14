<?php

namespace Boom;

abstract class Asset
{
	/**
	 *
	 * @var \Model_Asset
	 */
	protected $_model;

	public function __construct(\Model_Asset $model)
	{
		$this->_model = $model;
	}

	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->_model, $name), $arguments);
	}

	public static function directory()
	{
		return APPPATH . 'assets';
	}

	public function exists()
	{
		return $this->_model->id && file_exists($this->getFilename());
	}

	public static function factory(\Model_Asset $asset)
	{
		$classname = "\Boom\Asset\\Type\\" . Asset\Type::numericTypeToClass($asset->type);

		return new $classname($asset);
	}

	public function getId()
	{
		return $this->_model->id;
	}

	public function getFilename()
	{
		return static::directory() . DIRECTORY_SEPARATOR . $this->getId();
	}

	public function getVisibleFrom()
	{
		return new \DateTime('@' . $this->_model->visible_from);
	}

	abstract public function getType();
}