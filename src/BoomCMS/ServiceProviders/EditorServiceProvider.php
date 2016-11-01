<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Editor\Editor;
use BoomCMS\Routing\Router;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;
use Illuminate\Support\ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $guard, Store $session, Router $router)
    {
        $activePage = $router->getActivePage();

        $this->app->singleton(Editor::class, function () use($guard, $session, $activePage) {
            $default = ($guard->check() && $activePage && $guard->user()->can('toolbar', $activePage)) ? Editor::EDIT : Editor::DISABLED;
            
            return new Editor($session, $default);
        });
    }

    /**
     * @return void
     */
    public function register()
    {
    }
}
