<?php

namespace Boom\Asset;

use Boom\Person;
use \File;
use \DateTime;

abstract class Asset
{
    /**
	 *
	 * @var \Model_Asset
	 */
    protected $model;

    protected $old_files = [];

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
        return $this->exists() ? Mimetype::factory(File::mime($this->getFilename())) : null;
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
        if ( ! $this->loaded()) {
            return [];
        }

        if ($this->old_files === null) {
            // Add files for previous versions of the asset.
            // Wrap the glob in array_reverse() so that we end up with an array with the most recent first.
            foreach (new OldFilesIterator($this) as $file) {
                // Get the version ID out of the filename.
                preg_match('/' . $this->id . '.(\d+).bak$/', $file->getFilename(), $matches);

                if (isset($matches[1])) {
                    $this->old_files[$matches[1]] = $file;
                } else {
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

    public function getThumbnailAssetId()
    {
        return $this->model->thumbnail_asset_id;
    }

    public function getThumbnail()
    {
        return Factory::byId($this->getThumbnailAssetId());
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

    /**
	 *
	 * @return \Boom\Asset\Asset
	 */
    public function save()
    {
        $this->model->loaded() ? $this->model->update() : $this->model->create();

        return $this;
    }

    /**
	 *
	 * @param string $credits
	 * @return \Boom\Asset\Asset
	 */
    public function setCredits($credits)
    {
        $this->model->credits = $credits;

        return $this;
    }

    /**
	 *
	 * @param string $description
	 * @return \Boom\Asset\Asset
	 */
    public function setDescription($description)
    {
        $this->model->description = $description;

        return $this;
    }

    /**
	 *
	 * @param string $filename
	 * @return \Boom\Asset\Asset
	 */
    public function setFilename($filename)
    {
        $this->model->filename = $filename;

        return $this;
    }

    /**
	 *
	 * @param float $size
	 * @return \Boom\Asset\Asset
	 */
    public function setFilesize($size)
    {
        $this->model->filesize = $size;

        return $this;
    }

    /**
	 *
	 * @param DateTime $time
	 * @return \Boom\Asset\Asset
	 */
    public function setLastModified(DateTime $time)
    {
        $this->model->last_modified = $time->getTimestamp();

        return $this;
    }

    /**
	 *
	 * @param int $assetId
	 * @return \Boom\Asset\Asset
	 */
    public function setThumbnailAssetId($assetId)
    {
        $this->model->thumbnail_asset_id = $assetId;

        return $this;
    }

    /**
	 *
	 * @param string $title
	 * @return \Boom\Asset\Asset
	 */
    public function setTitle($title)
    {
        $this->model->title = $title;

        return $this;
    }

    /**
	 *
	 * @param \Boom\Person\Person $person
	 * @return \Boom\Asset\Asset
	 */
    public function setUploadedBy(Person\Person $person)
    {
        $this->model->uploaded_by = $person->getId();

        return $this;
    }

    public function setVisibleFrom(DateTime $time)
    {
        $this->model->visible_from = $time->getTimestamp();

        return $this;
    }
}
