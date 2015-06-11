<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app->bind('html', function($app) {
            return new Illuminate\Html\HtmlBuilder($app['url']);
        });

        $app->bind('boomcms.asset.provider', function($app) {});
        $app->bind('boomcms.chunk', function($app) {});

        return $app;
    }

    protected function getMockSession()
    {
        return $this
            ->getMockBuilder('Illuminate\Session\SessionManager')
            ->disableOriginalConstructor()
            ->getMock();
    }
}