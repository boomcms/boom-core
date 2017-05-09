<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\Asset as AssetRepositoryInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Foundation\Repository;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Imagick;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Asset extends Repository implements AssetRepositoryInterface
{
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
        Filesystem $filesystem)
    {
        $this->model = $model;
        $this->version = $version;
        $this->filesystem = $filesystem;
    }

    /**
     * Create an asset from an uploaded file, setting default values.
     *
     * @param UploadedFile $file
     *
     * @return int
     */
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

    /**
     * {@inheritdoc}
     *
     * @param AssetInterface $asset
     *
     * @return bool
     */
    public function exists(AssetInterface $asset): bool
    {
        return $this->filesystem->exists($asset->getLatestVersionId());
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

    public function file(AssetInterface $asset): string
    {
        return $this->filesystem->get($asset->getLatestVersionId());
    }

    protected function getThumbnailFilename(AssetInterface $asset): string
    {
        return $asset->getLatestVersionId().'.thumb';
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

    /**
     * {@inheritdoc}
     *
     * @param AssetInterface $asset
     */
    public function stream(AssetInterface $asset)
    {
        return $this->filesystem->readStream($asset->getLatestVersionId());
    }

    public function thumbnail(AssetInterface $asset): string
    {
        return $this->filesystem->get($this->getThumbnailFilename($asset));
    }
}
