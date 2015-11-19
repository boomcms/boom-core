<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Editor;
use Illuminate\Support\ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('boomcms.editor', function ($app) {
            return new Editor\Editor($app['boomcms.auth'], $app['session']);
        });
    }

    /**
     * @return void
     */
    public function register()
    {
    }
}
