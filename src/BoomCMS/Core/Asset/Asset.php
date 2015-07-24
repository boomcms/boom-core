<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Core\Person;
use DateTime;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;
use Rych\ByteSize\ByteSize;

abstract class Asset implements Arrayable
{
    /**
     *
     * @var array
     */
    protected $attributes;

    protected $old_files = [];

    protected $tags;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public static function directory()
    {
        return storage_path() . '/boomcms/assets';
    }

    public static function factory(array $attributes)
    {
        $type = Type::numericTypeToClass($attributes['type']) ?: 'Invalid';
        $classname = "BoomCMS\Core\Asset\Type\\" . $type;

        return new $classname($attributes);
    }

    public function exists()
    {
        return $this->loaded() && file_exists($this->getFilename());
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function getAspectRatio()
    {
        return 1;
    }

    public function getCredits()
    {
        return $this->get('credits');
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
        return $this->get('description');
    }

    public function getDownloads()
    {
        return $this->get('downloads');
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
        return $this->get('filesize');
    }

    public function getHumanFilesize()
    {
        return ByteSize::formatBinary($this->getFilesize());
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getLastModified()
    {
        return new DateTime('@' . $this->get('last_modified'));
    }

    public function getMimetype()
    {
        if ($this->exists()) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $this->getFilename());
            finfo_close($finfo);

            return Mimetype\Mimetype::factory($mime);
        }
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
        return $this->get('filename');
    }

    public function getTags()
    {
        if ($this->tags === null) {
            $this->tags = DB::table('assets_tags')
                ->where('asset_id', '=', $this->getId())
                ->lists('tag');
        }

        return $this->tags;
    }

    public function getThumbnailAssetId()
    {
        return $this->get('thumbnail_asset_id');
    }

    public function getThumbnail()
    {
        return Factory::byId($this->getThumbnailAssetId());
    }

    public function getTitle()
    {
        return $this->get('title');
    }

    abstract public function getType();

    public function getUploadedBy()
    {
        $provider = new Person\Provider();

        return $provider->findById($this->get('uploaded_by'));
    }

    public function getUploadedTime()
    {
        return (new DateTime())->setTimestamp($this->get('uploaded_time'));
    }

    public function incrementDownloads()
    {
        if ($this->loaded()) {
            DB::table('assets')
                ->where('id', '=', $this->getId())
                ->update([
                    'downloads' => DB::raw('downloads + 1')
                ]);
        }
    }

    public function isImage()
    {
        return false;
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    /**
	 *
	 * @param string $credits
	 * @return \Boom\Asset\Asset
	 */
    public function setCredits($credits)
    {
        $this->attributes['credits'] = $credits;

        return $this;
    }

    /**
	 *
	 * @param string $description
	 * @return \Boom\Asset\Asset
	 */
    public function setDescription($description)
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    /**
	 *
	 * @param string $filename
	 * @return \Boom\Asset\Asset
	 */
    public function setFilename($filename)
    {
        $this->attributes['filename'] = $filename;

        return $this;
    }

    /**
	 *
	 * @param float $size
	 * @return \Boom\Asset\Asset
	 */
    public function setFilesize($size)
    {
        $this->attributes['filesize'] = $size;

        return $this;
    }

    public function setId($id)
    {
        if ( !$this->getId()) {
            $this->attributes['id'] = $id;
        }

        return $this;
    }

    /**
	 *
	 * @param DateTime $time
	 * @return \Boom\Asset\Asset
	 */
    public function setLastModified(DateTime $time)
    {
        $this->attributes['last_modified'] = $time->getTimestamp();

        return $this;
    }

    /**
	 *
	 * @param int $assetId
	 * @return \Boom\Asset\Asset
	 */
    public function setThumbnailAssetId($assetId)
    {
        $this->attributes['thumbnail_asset_id'] = $assetId;

        return $this;
    }

    /**
	 *
	 * @param string $title
	 * @return \Boom\Asset\Asset
	 */
    public function setTitle($title)
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    /**
	 *
	 * @param \Boom\Person\Person $person
	 * @return \Boom\Asset\Asset
	 */
    public function setUploadedBy(Person\Person $person)
    {
        $this->attributes['uploaded_by'] = $person->getId();

        return $this;
    }

    public function setUploadedTime(DateTime $time)
    {
        $this->attributes['uploaded_time'] = $time->getTimestamp();

        return $this;
    }

    public function toArray()
    {
        return $this->attributes;
    }
}
