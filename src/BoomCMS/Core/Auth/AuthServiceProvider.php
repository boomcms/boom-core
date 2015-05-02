<?php

namespace BoomCMS\Core\Auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->app->singleton('BoomAuth', function($app)
        {
            return new Auth($app['session'], $app['BoomPersonProvider']);
        });
    }

	/**
	 *
	 * @return void
	 */
	public function register()
	{
	}

}
