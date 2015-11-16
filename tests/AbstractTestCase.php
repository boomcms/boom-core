<?php

namespace BoomCMS\Tests;

use BoomCMS\Core\Auth\PermissionsProvider;
use BoomCMS\Repositories;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Session\SessionManager;

abstract class AbstractTestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        $app->register(Stubs\BoomCMSServiceProvider::class);

        $app->bind('boomcms.repositories.person', function ($app) {
            return new Stubs\PersonRepository();
        });

        $app->bind('boomcms.settings', function ($app) {
            return new Stubs\SettingsStore();
        });

        return $app;
    }

    protected function getMockSession()
    {
        return $this
            ->getMockBuilder(SessionManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockPersonRepository($methods = null)
    {
        return $this
            ->getMockBuilder(Repositories\Person::class)
            ->setMethods($methods)
            ->getMock();
    }

    protected function getMockPermissionsProvider()
    {
        return $this->getMock(PermissionsProvider::class);
    }
}
