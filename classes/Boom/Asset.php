<?php

namespace Boom;

use \DateTime as DateTime;
use \File as File;

abstract class Asset
{
	/**
	 *
	 * @var \Model_Asset
	 */
	protected $model;

	public function __construct(\Model_Asset $model)
	{
		$this->model = $model;
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

	public function getAspectRatio()
	{
		return 1;
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

	public function getDescription()
	{
		return $this->model->description;
	}

	public function getDownloads()
	{
		return $this->model->downloads;
	}

	public function getId()
	{
		return $this->model->id;
	}

	public function getLastModified()
	{
		return new DateTime('@' . $this->model->last_modified);
	}

	public function getMimetype()
	{
		return $this->exists()? Asset\Mimetype::factory(File::mime($this->getFilename())) : null;
	}

	public function getTitle()
	{
		return $this->model->title;
	}

	public function getVisibleFrom()
	{
		return new \DateTime('@' . $this->model->visible_from);
	}

	abstract public function getType();

	public function isVisible()
	{
		return $this->getVisibleFrom()->getTimestamp() < time();
	}

	public function loaded()
	{
		return $this->model->loaded();
	}
}