<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Foundation\Repository;
use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Imagick;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Asset extends Repository
{
    /**
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var AssetModel
     */
    protected $model;

    /**
     *
     * @var AssetVersionRepository
     */
    protected $version;

    public function __construct(
        AssetModel $model,
        AssetVersionRepository $version,
        Filesystem $filesystem)
    {
        $this->model = $model;
        $this->version = $version;
        $this->filesystem = $filesystem;
    }

    public function createFromFile(UploadedFile $file): AssetInterface
    {
        $info = FileInfo::create($file);

        $asset = new AssetModel();
        $asset
            ->setTitle($info->getTitle() ?: $file->getClientOriginalName())
            ->setPublishedAt($info->getCreatedAt())
            ->setType(AssetHelper::typeFromMimetype($file->getMimeType()))
            ->setDescription($info->getDescription())
            ->setCredits($info->getCopyright());

        $this->save($asset);

        $this->version->createFromFile($asset, $file, $info);

        $this->saveFile($asset, $file, $info->getThumbnail());

        return $asset;
    }

    public function delete($param): Repository
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

    public function exists(AssetInterface $asset): bool
    {
        return $this->filesystem->exists($asset->getLatestVersionId());
    }

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
        return $this->filesystem->path($asset->getLatestVersionId());
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

    public function saveFile(AssetInterface $asset, UploadedFile $file, Imagick $thumbnail = null)
    {
        $this->filesystem->putFileAs(null, $file, $asset->getLatestVersionId());

        if ($thumbnail) {
            $this->filesystem->put($this->getThumbnailFilename($asset), $thumbnail->getImageBlob());
        }
    }

    public function stream(AssetInterface $asset)
    {
        return $this->filesystem->readStream($asset->getLatestVersionId());
    }

    public function thumbnail(AssetInterface $asset)
    {
        return $this->filesystem->get($this->getThumbnailFilename($asset));
    }
}
