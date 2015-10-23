<?php

use Illuminate\Contracts\Console\Kernel;

class TestCase extends Illuminate\Foundation\Testing\TestCase
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
        $app->register('CoreServiceProviderStub');

        $app->bind('boomcms.person.provider', function ($app) {
            return new PersonProvider();
        });

        $app->bind('boomcms.settings', function ($app) {
            return new SettingsStore();
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
