<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Page;
use BoomCMS\Policies\PagePolicy;
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
    ];

    public function boot(Gate $gate)
    {
        $this->registerPolicies($gate);
    }
}
