<?php

namespace BoomCMS\FileInfo;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->app->singleton(FileInfo::class, function () {
            return new FileInfo();
        });
    }

    /**
     * @return void
     */
    public function register()
    {
    }
}
