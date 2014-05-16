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

	public static function directory()
	{
		return APPPATH . 'assets';
	}

	public function exists()
	{
		return $this->loaded() && file_exists($this->getFilename());
	}

	public static function factory(\Model_Asset $asset)
	{
		$type = Asset\Type::numericTypeToClass($asset->type)?: 'Invalid';
		$classname = "\Boom\Asset\\Type\\" . $type;

		return new $classname($asset);
	}

	/**
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->getMimetype()->getExtension();
	}

	/**
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return static::directory() . DIRECTORY_SEPARATOR . $this->getId();
	}

	public function getId()
	{
		return $this->_model->id;
	}

	public function getMimetype()
	{
		return $this->exists()? Asset\Mimetype::factory(\File::mime($this->getFilename())) : null;
	}

	public function getTitle()
	{
		return $this->_model->title;
	}

	public function getVisibleFrom()
	{
		return new \DateTime('@' . $this->_model->visible_from);
	}

	abstract public function getType();

	public function isVisible()
	{
		return $this->getVisibleFrom()->getTimestamp() < time();
	}

	public function loaded()
	{
		return $this->_model->loaded();
	}
}