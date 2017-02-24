<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Repositories\Asset as AssetRepositoryInterface;
use BoomCMS\Contracts\Repositories\AssetVersion as AssetVersionRepositoryInterface;
use BoomCMS\Database\Models\Asset as AssetModel;
use BoomCMS\Database\Models\AssetVersion as AssetVersionModel;
use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
use Imagick;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Asset implements AssetRepositoryInterface
{
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
    public function createFromFile(UploadedFile $file): int
    {
        $info = FileInfo::create($file);

        $asset = new AssetModel();
        $asset
            ->setTitle($info->getTitle() ?: $file->getClientOriginalName())
            ->setPublishedAt($info->getCreatedAt())
            ->setType(AssetHelper::typeFromMimetype($file->getMimeType()))
            ->setDescription($info->getDescription())
            ->setCredits($info->getCopyright());

        $assetId = $this->save($asset)->getId();

        $this->version->createFromFile($asset, $file, $info);

        $this->saveFile($asset, $file, $info->getThumbnail());

        return $assetId;
    }

    /**
     * @param array $assetIds
     *
     * @return $this
     */
    public function delete(array $assetIds)
    {
        $this->model->destroy($assetIds);

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
        return $this->filesystem->exists($asset->getLatestVersionId());
    }

    /**
     * Returns an array of extensions which are in use with the latest versions
     *
     * @return array
     */
    public function extensions(): array
    {
        return $this->model
            ->withLatestVersion()
            ->select('version.'.AssetVersionModel::ATTR_EXTENSION.' as e')
            ->having('e', '!=', '')
            ->orderBy('e')
            ->distinct()
            ->pluck('e')
            ->toArray();
    }

    public function file(AssetInterface $asset): string
    {
        return $this->filesystem->get($asset->getLatestVersionId());
    }

    /**
     * @param int $assetId
     *
     * @return AssetModel
     */
    public function find($assetId)
    {
        return $this->model->find($assetId);
    }

    protected function getThumbnailFilename(AssetInterface $asset)
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
        $version = $this->find($versionId);

        if ($version && $version->getAssetId() == $asset->getId()) {
            $attrs = $version->toArray();
            unset($attrs['id']);

            $version = $this->model->create($attrs);

            $this->filesystem->copy($versionId, $version->getId());
        }

        return $asset;
    }

    /**
     * @param AssetInterface $model
     *
     * @return AssetModel
     */
    public function save(AssetModel $model)
    {
        $model->save();

        return $model;
    }

    public function saveFile(AssetInterface $asset, UploadedFile $file, Imagick $thumbnail = null)
    {
        $this->filesystem->putFileAs(null, $file, $asset->getLatestVersionId());

        if ($thumbnail) {
            $this->filesystem->put($this->getThumbnailFilename($version), $thumbnail->getImageBlob());
        }
    }

    public function thumbnail(AssetInterface $asset): string
    {
        return $this->filesystem->get($this->getThumbnailFilename($asset));
    }

    /**
     * Returns a Collection of People who have uploaded assets.
     *
     * @return Collection
     */
    public function uploaders(PersonModel $model = null)
    {
        $model = $model ?: new PersonModel();

        return $model
            ->select('people.*')
            ->join('assets', 'assets.'.AssetModel::ATTR_CREATED_BY, '=', 'people.id')
            ->groupBy('people.id')
            ->orderBy(PersonModel::ATTR_NAME)
            ->get();
    }
}
