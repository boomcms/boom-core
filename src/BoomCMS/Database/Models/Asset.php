<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Support\Traits\Comparable;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;

class Asset extends Model implements AssetInterface
{
    use Comparable;

    const ATTR_ID = 'id';
    const ATTR_TITLE = 'title';
    const ATTR_DESC = 'description';
    const ATTR_TYPE = 'type';
    const ATTR_UPLOADED_BY = 'uploaded_by';
    const ATTR_UPLOADED_AT = 'uploaded_time';
    const ATTR_THUMBNAIL_ID = 'thumbnail_asset_id';
    const ATTR_CREDITS = 'credits';
    const ATTR_DOWNLOADS = 'downloads';

    public $table = 'assets';

    public $guarded = [
        self::ATTR_ID
    ];

    public $timestamps = false;

    /**
     * @var PersinInterface
     */
    protected $uploadedBy;

    public function directory()
    {
        return storage_path().'/boomcms/assets';
    }

    public function exists()
    {
        return $this->getId() && file_exists($this->getFilename());
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->getLatestVersion()->getExtension();
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->directory().DIRECTORY_SEPARATOR.$this->getLatestVersionId();
    }

    public function getFilesize()
    {
        return $this->get('filesize');
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->getLatestVersion()->getHeight();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return  (int) $this->{self::ATTR_ID};
    }

    public function getEmbedHtml($height = null, $width = null)
    {
        $viewPrefix = 'boomcms::assets.embed.';
        $assetType = strtolower(class_basename($this->getType()));

        $viewName = View::exists($viewPrefix.$assetType) ?
            $viewPrefix.$assetType :
            $viewPrefix.'default';

        return View::make($viewName, [
            'asset'  => $this,
            'height' => $height,
            'width'  => $width,
        ]);
    }

    /**
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->getLatestVersion()->getEditedAt();
    }

    /**
     * @return AssetVersion
     */
    public function getLatestVersion()
    {
        return $this->latestVersion->first()->first();
    }

    public function getLatestVersionId()
    {
        return $this->getLatestVersion()->getId();
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->getLatestVersion()->getMimetype();
    }

    public function getOriginalFilename()
    {
        return $this->getLatestVersion()->getFilename();
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
        return AssetFacade::find($this->getThumbnailAssetId());
    }

    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->{self::ATTR_TYPE};
    }

    /**
     * @return PersonInterface
     */
    public function getUploadedBy()
    {
        if ($this->uploadedBy === null) {
            $this->uploadedBy = $this->hasOne(Person::class, 'uploaded_by')->first();
        }

        return $this->uploadedBy;
    }

    public function getUploadedTime()
    {
        return (new DateTime())->setTimestamp($this->get('uploaded_time'));
    }

    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->getLatestVersion()->getWidth();
    }

    /**
     * @return bool
     */
    public function hasPreviousVersions()
    {
        return $this->versions
            ->where('id', '!=', $this->getLatestVersionId())
            ->exists();
    }

    public function hasThumbnail()
    {
        return $this->getThumbnailAssetId() > 0;
    }

    /**
     * @return $this
     */
    public function incrementDownloads()
    {
        $this->increment(self::ATTR_DOWNLOADS);

        return $this;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->getType() == 'image';
    }

    public function createVersionFromFile(File $file)
    {
        if (!$this->getTitle()) {
            $this->setTitle($file->getClientOriginalName());
        }

        list($width, $height) = getimagesize($file->getRealPath());
        preg_match('|\.([a-z]+)$|', $file->getClientOriginalName(), $extension);

        $version = AssetVersion::create([
            'asset_id'  => $this->getId(),
            'extension' => $extension[1],
            'filesize'  => $file->getClientSize(),
            'filename'  => $file->getClientOriginalName(),
            'width'     => $width,
            'height'    => $height,
            'edited_at' => time(),
            'edited_by' => Auth::getPerson()->getId(),
            'mimetype'  => File::mime($file->getRealPath()),
        ]);

        $this->setType(AssetHelper::typeFromMimetype($file->getMimeType()));

        $file->move(static::directory(), $version->id);

        return $this;
    }

    public function revertTo($versionId)
    {
        $version = AssetVersion::find($versionId);

        if ($version && $version->asset_id = $this->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);
            $attrs['edited_at'] = time();
            $attrs['edited_by'] = Auth::getPerson()->getId();

            $version = VersionModel::create($attrs);

            copy(
                static::directory().DIRECTORY_SEPARATOR.$versionId,
                static::directory().DIRECTORY_SEPARATOR.$version->id
            );
        }

        return $this;
    }

    /**
     * @param string $credits
     *
     * @return $this
     */
    public function setCredits($credits)
    {
        $this->attributes['credits'] = $credits;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    /**
     * @param int $assetId
     *
     * @return $this
     */
    public function setThumbnailAssetId($assetId)
    {
        $this->attributes['thumbnail_asset_id'] = $assetId;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    /**
     * @param PersonInterface $person
     *
     * @return $this
     */
    public function setUploadedBy(PersonInterface $person)
    {
        $this->attributes['uploaded_by'] = $person->getId();

        return $this;
    }

    public function setUploadedTime(DateTime $time)
    {
        $this->attributes['uploaded_time'] = $time->getTimestamp();

        return $this;
    }

    public function latestVersion()
    {
        return $this->versions()
            ->leftJoin('asset_versions as av2', function ($query) {
                $query
                    ->on('av2.asset_id', '=', 'asset_versions.asset_id')
                    ->on('av2.id', '>', 'asset_versions.id');
            })
            ->whereNull('av2.id');
    }

    public function scopeWithVersion($query, $versionId)
    {
        return $query
            ->where('asset_versions.id', '=', $versionId)
            ->first();
    }

    public function versions()
    {
        return $this->hasMany(AssetVersion::class);
    }
}
