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
        $this->app->singleton('BoomEnvironment', function($app)
        {
            $envName = ucfirst(strtolower(env('BoomCMS_ENV'), 'Production'));
            $className = 'Environment\\' . $envName;

            if ( ! class_exists($className)) {
                throw new Environment\InvalidEnvironmentException($envName);
            }

            return new $className;
        });
	}

}
