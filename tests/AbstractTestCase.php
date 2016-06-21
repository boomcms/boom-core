<?php

namespace BoomCMS\Tests;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Support\Facades\Router;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected $baseUrl = 'localhost';

    public function setUp()
    {
        parent::setUp();

        $this->site = new Site();

        Router::setActiveSite($this->site);
    }

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
