<?php

namespace BoomCMS\Core\Exception;

use BoomCMS\Core\Exception\Handler as ExceptionHandler;
use Exception;

class ExceptionHandlerServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $environment = $this->app['boomcms.environment'];

        if ($environment->registerExceptionHandler()) {
            set_exception_handler(function(Exception $e) {
                $handler = new ExceptionHandler();
                $handler->handle($e);
            });
        }
    }

    /**
     *
     * @return void
     */
    public function register()
    {
    }
}
