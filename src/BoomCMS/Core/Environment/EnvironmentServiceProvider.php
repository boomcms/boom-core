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
        $envName = ucfirst(strtolower($this->app->environment()));
        $namespace = 'BoomCMS\Core\Environment\\';
        $className = $namespace . $envName;

        if ( ! class_exists($className)) {
            $className =$namespace . 'Development';
        }

        $this->app->singleton('boomcms.environment', function ($app) use ($className) {
            return new $className();
        });

        $this->app->bind('BoomCMS\Core\Environment\Environment', $className);
    }

}
