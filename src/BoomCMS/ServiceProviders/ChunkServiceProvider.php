<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Chunk;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;

class ChunkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('boomcms.chunk', function ($app) {
            return new Chunk\Provider($app[Gate::class], $app['cache.store']);
        });
    }

    /**
     * @return void
     */
    public function register()
    {
    }
}
