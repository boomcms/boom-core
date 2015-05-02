<?php

namespace BoomCMS\Core\Editor;

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
        $this->app->singleton('BoomEditor', function($app)
        {
            return new Editor($app['BoomAuth'], $app['session']);
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
