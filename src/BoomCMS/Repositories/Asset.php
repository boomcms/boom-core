<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\Asset as AssetRepositoryInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Contracts\Repositories\Repository as RepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Foundation\Repository;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Imagick;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Asset extends Repository implements AssetRepositoryInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystems;

    /**
     * @var AssetModel
     */
    protected $model;

    /**
     * @var AssetVersionRepositoryInterface
     */
    protected $version;

    /**
     * @param AssetModel        $model
     * @param AsserVersionModel $version
     */
    public function __construct(
        AssetModel $model,
        AssetVersionRepositoryInterface $version,
        Filesystem $filesystems)
    {
        $this->model = $model;
        $this->version = $version;
        $this->filesystems = $filesystems;
    }

    /**
     * Create an asset from an uploaded file, setting default values.
     *
     * @param UploadedFile $file
     *
     * @return int
     */
    public function createFromUploadedFile(UploadedFile $file): AssetInterface
    {
        $diskName = Config::get('filesystems.assets');

        $disk = $this->filesystems->disk($diskName);
        $path = $disk->put('', $file);

        $info = FileInfo::create($disk, $path);
        $info->setOriginalFilename($file->getClientOriginalName());

        return $this->createFromFile($diskName, $info);
    }

    public function createFromFile(string $disk, FileInfoDriver $file): AssetInterface
    {
        $asset = new AssetModel();
        $asset
            ->setTitle($file->getTitle() ?: $file->getFilename())
            ->setPublishedAt($file->getCreatedAt())
            ->setType($file->getAssetType())
            ->setDescription($file->getDescription())
            ->setCredits($file->getCopyright());

        $this->save($asset);

        $this->version->createFromFile($asset, $disk, $file);

        if (!$asset->isImage() && $file->getThumbnail()) {
            $this->saveThumbnail($asset, $disk, $file->getThumbnail());
        }

        return $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($param): RepositoryInterface
    {
        $assetIds = is_array($param) ? $param : [$param->getId()];
        $albums = AlbumFacade::findByAssetIds($assetIds);

        parent::delete($param);

        // Update the asset counts and feature image of the albums which these assets appeared in.
        foreach ($albums as $album) {
            $album->assetsUpdated();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param AssetInterface $asset
     *
     * @return bool
     */
    public function exists(AssetInterface $asset): bool
    {
        return $this->filesystem($asset)->exists($asset->getPath());
    }

    /**
     * Returns an array of extensions which are in use with the latest versions.
     *
     * @return Collection
     */
    public function extensions(): Collection
    {
        return $this->model
            ->withLatestVersion()
            ->select('version.'.AssetVersionModel::ATTR_EXTENSION.' as e')
            ->having('e', '!=', '')
            ->orderBy('e')
            ->distinct()
            ->pluck('e');
    }

    public function filesystem(AssetInterface $asset)
    {
        return $this->filesystems->disk($asset->getLatestVersion()->getFilesystem());
    }

    /**
     * @param int $assetId
     *
     * @return AssetModel|null
     */
    public function find($assetId)
    {
        return $this->model->find($assetId);
    }

    protected function getThumbnailFilename(AssetInterface $asset): string
    {
        return $asset->getLatestVersionId().'.thumb';
    }

    public function path(AssetInterface $asset): string
    {
        return $this->filesystem($asset)->path($asset->getPath());
    }

    public function replaceWith(AssetInterface $asset, UploadedFile $file)
    {
        $info = FileInfo::create($file);

        $asset->setType(AssetHelper::typeFromMimetype($file->getMimeType()));
        $this->save($asset);

        $this->version->createFromFile($asset, $file);

        $this->saveFile($asset, $file, $info->getThumbnail());
    }

    public function revert(AssetInterface $asset, $versionId)
    {
        $version = $this->version->find($versionId);

        if ($version && $version->getAssetId() === $asset->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);

            $version = $this->version->create($attrs);

            $this->filesystem->copy($versionId, $version->getId());
        }

        return $asset;
    }

    public function saveThumbnail(AssetInterface $asset, string $disk, Imagick $thumbnail = null)
    {
        $this->filesystems
            ->disk($disk)
            ->put($this->getThumbnailFilename($asset), $thumbnail->getImageBlob());
    }

    public function thumbnail(AssetInterface $asset)
    {
        return $this->filesystem($asset)->get($this->getThumbnailFilename($asset));
    }
}
