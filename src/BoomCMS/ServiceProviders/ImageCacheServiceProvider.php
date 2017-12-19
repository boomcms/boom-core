<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Asset\ImageCache;
use BoomCMS\Repositories\Asset as AssetRepository;
use Illuminate\Cache\CacheManager;
use Intervention\Image\ImageManager;

class ImageCacheServiceProvider extends ServiceProvider
{
    public function boot(AssetRepository $repository, CacheManager $cache): void
    {
        $this->app->singleton(ImageCache::class, function () use ($repository, $cache) {
            $imageManager = new ImageManager(['driver' => 'imagick']);

            return new ImageCache($repository, $imageManager, $cache);
        });
    }
}
