<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Core\Person;
use BoomCMS\Database\Models\Asset\Version as VersionModel;
use BoomCMS\Core\Asset\Mimetype\Mimetype;
use BoomCMS\Support\Facades\Auth;
use DateTime;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;
use Rych\ByteSize\ByteSize;

abstract class Asset implements Arrayable
{
    /**
     *
     * @var array
     */
    protected $attrs;

    protected $hasPreviousVersions;

    protected $tags;

    protected $versionColumns = [
        'asset_id' => '',
        'width' => '',
        'height' => '',
        'type' => '',
        'filesize' => '',
        'filename' => '',
        'edited_at' => '',
        'edited_by' => '',
        'version:id' => '',
    ];

    public function __construct(array $attrs)
    {
        $this->attrs = $attrs;
    }

    public static function directory()
    {
        return storage_path() . '/boomcms/assets';
    }

    public static function factory(array $attrs)
    {
        $type = Type::numericTypeToClass($attrs['type']) ?: 'Invalid';
        $classname = "BoomCMS\Core\Asset\Type\\" . $type;

        return new $classname($attrs);
    }

    public function exists()
    {
        return $this->loaded() && file_exists($this->getFilename());
    }

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getAspectRatio()
    {
        return 1;
    }

    public function getCredits()
    {
        return $this->get('credits');
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
        return static::directory() . DIRECTORY_SEPARATOR . $this->getLatestVersionId();
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
        return new DateTime('@' . $this->get('edited_at'));
    }

    public function getLatestVersionId()
    {
        return $this->get('version:id');
    }

    public function getMimetype()
    {
        if ($this->exists()) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $this->getFilename());
            finfo_close($finfo);

            return Mimetype::factory($mime);
        }
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

    public function getVersions()
    {
        return VersionModel::forAsset($this)->get();
    }

    public function hasPreviousVersions()
    {
        if ($this->hasPreviousVersions === null) {
            $result = DB::table('asset_versions')
                ->select("id")
                ->where('asset_id', '=', $this->getId())
                ->where('id', '!=', $this->getLatestVersionId())
                ->first();

            $this->hasPreviousVersions = isset($result->id);
        }

        return $this->hasPreviousVersions;
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

    public function createVersionFromFile(File $file)
    {
        if ( ! $this->getTitle()) {
            $this->setTitle($file->getClientOriginalName());
        }

        list($width, $height) = getimagesize($file->getRealPath());

        $version = VersionModel::create([
            'asset_id' => $this->getId(),
            'filesize' => $file->getClientSize(),
            'filename' => $file->getClientOriginalName(),
            'width' => $width,
            'height' => $height,
            'edited_at' => time(),
            'edited_by' => Auth::getPerson()->getId(),
            'type' => Mimetype::factory($file->getMimeType())->getType(),
        ]);

        $file->move(static::directory(), $version->id);

        return $this;
    }

    public function revertTo($versionId)
    {
        $version = VersionModel::find($versionId);

        if ($version && $version->asset_id = $this->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);
            $attrs['edited_at'] = time();
            $attrs['edited_by'] = Auth::getPerson()->getId();

            $version = VersionModel::create($attrs);

            copy(
                static::directory() . DIRECTORY_SEPARATOR . $versionId,
                static::directory() . DIRECTORY_SEPARATOR . $version->id
            );
        }

        return $this;
    }

    /**
	 *
	 * @param string $credits
	 * @return \Boom\Asset\Asset
	 */
    public function setCredits($credits)
    {
        $this->attrs['credits'] = $credits;

        return $this;
    }

    /**
	 *
	 * @param string $description
	 * @return \Boom\Asset\Asset
	 */
    public function setDescription($description)
    {
        $this->attrs['description'] = $description;

        return $this;
    }

    public function setId($id)
    {
        if ( !$this->getId()) {
            $this->attrs['id'] = $id;
        }

        return $this;
    }

    /**
	 *
	 * @param int $assetId
	 * @return \Boom\Asset\Asset
	 */
    public function setThumbnailAssetId($assetId)
    {
        $this->attrs['thumbnail_asset_id'] = $assetId;

        return $this;
    }

    /**
	 *
	 * @param string $title
	 * @return \Boom\Asset\Asset
	 */
    public function setTitle($title)
    {
        $this->attrs['title'] = $title;

        return $this;
    }

    /**
	 *
	 * @param \Boom\Person\Person $person
	 * @return \Boom\Asset\Asset
	 */
    public function setUploadedBy(Person\Person $person)
    {
        $this->attrs['uploaded_by'] = $person->getId();

        return $this;
    }

    public function setUploadedTime(DateTime $time)
    {
        $this->attrs['uploaded_time'] = $time->getTimestamp();

        return $this;
    }

    public function toArray()
    {
        return array_diff_key($this->attrs, $this->versionColumns);
    }
}