<?php

namespace BoomCMS\Asset;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Repositories\Asset as AssetRepository;
use Illuminate\Cache\CacheManager;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

class ImageCache
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var string|null
     */
    protected $encoding = null;

    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var AssetRepository
     */
    protected $repository;

    public function __construct(
        AssetRepository $repository,
        ImageManager $imageManager,
        CacheManager $cache
    ) {
        $this->repository = $repository;
        $this->imageManager = $imageManager;
        $this->cache = $cache;
    }

    public function crop(Asset $asset, int $width = null, int $height = null): string
    {
        $key = "asset-crop-{$asset->getLatestVersionId()}-w$width-h$height";

        return $this->cache->rememberForever($key, function() use ($asset, $width, $height) {
            return $this->imageManager->make($this->repository->read($asset))
                ->fit($width, $height)
                ->encode($this->encoding);
        });
    }

    public function resize(Asset $asset, int $width = null, int $height = null): string
    {
        $key = "asset-{$asset->getLatestVersionId()}-w$width-h$height";

        $width = empty($width) ? null : $width;
        $height = empty($height) ? null : $height;

        return $this->cache->rememberForever($key, function() use($asset, $width, $height) {
            return $this->imageManager
                ->make($this->repository->read($asset))
                ->resize($width, $height, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($this->encoding); 
        });
    }

    public function setEncoding(string $encoding): ImageCache
    {
        $this->encoding = $encoding;

        return $this;
    }
}
