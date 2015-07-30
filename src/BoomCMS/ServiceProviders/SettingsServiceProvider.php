<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Settings;
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
        $this->app->singleton('boomcms.settings', function ($app) {
            return new Settings\Store($app['files']);
        });
    }

    /**
	 *
	 * @return void
	 */
    public function register() {}

}
