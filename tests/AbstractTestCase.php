<?php

namespace BoomCMS\Tests;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories\Person as PersonRepository;
use BoomCMS\Routing\Router;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use Mockery as m;

abstract class AbstractTestCase extends TestCase
{
    protected $baseUrl = 'localhost';

    /**
     * @var Site
     */
    protected $site;

    protected $people;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $this->site = new Site();
        $this->people = m::mock(PersonRepository::class);

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        $app->bind('boomcms.settings', function () {
            return new Stubs\SettingsStore();
        });

        $app->singleton(Router::class, function () use ($app) {
            $router = new Router($app);
            $router->setActiveSite($this->site);

            return $router;
        });

        $app->instance(Site::class, function () {
            return $this->site;
        });

        $app['config']->set('auth', require __DIR__.'/../src/config/auth.php');
        $app['config']->set('filesystems', require __DIR__.'/../src/config/filesystems.php');
        $app['config']->set('auth.providers.boomcms.driver', 'boomcms_test');

        $app['auth']->provider('boomcms_test', function () {
            return $this->people;
        });

        $app->register(Stubs\BoomCMSServiceProvider::class);

        require __DIR__.'/../src/routes.php';

        return $app;
    }

    protected function invalidPage()
    {
        return new Page();
    }

    protected function validPage($pageId = 1)
    {
        $page = new Page();
        $page->id = $pageId;

        return $page;
    }
}
