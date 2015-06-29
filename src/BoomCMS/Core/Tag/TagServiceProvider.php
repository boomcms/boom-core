<?php

namespace BoomCMS\Core\Tag;

use Illuminate\Support\ServiceProvider;

class TagServiceProvider extends ServiceProvider
{
	protected $deferred = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('boomcms.tag.provider', function ($app) {
            return new Provider();
        });
    }

    /**
     *
     * @return void
     */
    public function register() {}

}
