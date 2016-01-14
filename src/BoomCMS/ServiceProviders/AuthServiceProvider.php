<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Policies\PagePolicy;
use BoomCMS\Policies\PersonPolicy;
use BoomCMS\Policies\SitePolicy;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [
        Request::class => SitePolicy::class,
        Page::class    => PagePolicy::class,
        Person::class  => PersonPolicy::class,
    ];

    public function boot(Gate $gate)
    {
        $this->registerPolicies($gate);

        $this->app['auth']->extend('boomcms', function ()
        {
            return $this->app['boomcms.repositories.person'];
        });
    }
}
