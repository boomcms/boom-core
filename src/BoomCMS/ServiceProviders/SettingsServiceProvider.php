<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Settings;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.settings', function ($app) {
            return new Settings\Store($app['files']);
        });
    }
}
