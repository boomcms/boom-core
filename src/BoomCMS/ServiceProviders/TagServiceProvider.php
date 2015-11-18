<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Tag as TagModel;
use BoomCMS\Repositories\Tag;
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
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.repositories.tag', function ($app) {
            return new Tag(new TagModel());
        });
    }
}
