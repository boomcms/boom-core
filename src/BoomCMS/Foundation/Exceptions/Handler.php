<?php

namespace BoomCMS\Foundation\Exceptions;

use BoomCMS\Support\Facades\Page;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($this->isHttpException($e)) {
            $code = $e->getStatusCode();

            if ($code !== 500 || App::environment('production') || App::environment('staging')) {
                $page = Page::findByInternalName($code);

                if ($page) {
                    $request = Request::create($page->url()->getLocation(), 'GET');

                    return response(Route::dispatch($request)->getContent(), $code);
                }
            }
        }

        return parent::render($request, $e);
    }
}
