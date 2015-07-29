<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Chunk;
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
            return new Chunk\Provider($app['boomcms.auth'], $app['boomcms.editor']);
        });
    }

    /**
     *
     * @return void
     */
    public function register() {}

}
