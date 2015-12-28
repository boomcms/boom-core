<?php

namespace BoomCMS\Tests;

use BoomCMS\Database\Models\Page;
use BoomCMS\Repositories;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Session\SessionManager;

abstract class AbstractTestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected $baseUrl = 'localhost';

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
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function invalidPage()
    {
        return new Page();
    }

    protected function validPage($id = 1)
    {
        $page = new Page();
        $page->id = $id;

        return $page;
    }
}
