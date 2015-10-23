<?php

namespace BoomCMS\Tests;

use Illuminate\Contracts\Console\Kernel;

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
        $app->register(Stubs\CoreServiceProvider::class);

        $app->bind('boomcms.person.provider', function ($app) {
            return new Stubs\PersonProvider();
        });

        $app->bind('boomcms.settings', function ($app) {
            return new Stubs\SettingsStore();
        });

        return $app;
    }

    protected function getMockSession()
    {
        return $this
            ->getMockBuilder('Illuminate\Session\SessionManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockPersonProvider($methods = null)
    {
        return $this
            ->getMockBuilder('BoomCMS\Core\Person\Provider')
            ->setMethods($methods)
            ->getMock();
    }

    protected function getMockPermissionsProvider()
    {
        return $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');
    }
}
