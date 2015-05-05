<?php

namespace BoomCMS\Core\Environment;

use Illuminate\Support\ServiceProvider;

class EnvironmentServiceProvider extends ServiceProvider
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
	 *
	 * @return void
	 */
	public function register()
	{
        $envName = ucfirst(strtolower(env('BoomCMS_ENV', 'Production')));
        $className = 'BoomCMS\Core\Environment\\' . $envName;

        if ( ! class_exists($className)) {
            throw new Environment\InvalidEnvironmentException($envName);
        }

        $this->app->singleton('BoomEnvironment', function($app) use ($className)
        {
            return new $className;
        });

        $this->app->bind('BoomCMS\Core\Environment\Environment', $className);
	}

}
