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

	protected $old_files = array();

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

	public function getCredits()
	{
		return $this->model->credits;
	}

	/**
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->getMimetype()->getExtension();
	}

	public function getDescription()
	{
		return $this->model->description;
	}

	public function getDownloads()
	{
		return $this->model->downloads;
	}

	/**
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return static::directory() . DIRECTORY_SEPARATOR . $this->getId();
	}

	public function getFilesize()
	{
		return $this->model->filesize;
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

	/**
	 * Returns an array of old files which have been replaced.
	 * Where an asset has been replaced the array will contain the names of the backup files for the previous versions.
	 *
	 * @return	array
	 */
	public function getOldFiles()
	{
		// If the asset doesn't exist return an empty array.
		if ( ! $this->loaded())
		{
			return array();
		}

		if ($this->old_files === null)
		{
			// Add files for previous versions of the asset.
			// Wrap the glob in array_reverse() so that we end up with an array with the most recent first.
			foreach (new \Boom\Asset\OldFilesIterator($this) as $file)
			{
				// Get the version ID out of the filename.
				preg_match('/' . $this->id . '.(\d+).bak$/', $file->getFilename(), $matches);

				if (isset($matches[1]))
				{
					$this->old_files[$matches[1]] = $file;
				}
				else
				{
					$this->old_files[] = $file;
				}
			}
		}

		return $this->old_files;
	}

	public function getOriginalFilename()
	{
		return $this->model->filename;
	}

	public function getTitle()
	{
		return $this->model->title;
	}

	abstract public function getType();

	public function getUploadedBy()
	{
		return $this->model->uploader;
	}

	public function getUploadedTime()
	{
		return new \DateTime('@' . $this->model->uploaded_time);
	}

	public function getVisibleFrom()
	{
		return new \DateTime('@' . $this->model->visible_from);
	}

	public function isVisible()
	{
		return $this->getVisibleFrom()->getTimestamp() < time();
	}

	public function loaded()
	{
		return $this->model->loaded();
	}
}