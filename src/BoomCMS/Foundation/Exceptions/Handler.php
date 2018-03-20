<?php

namespace BoomCMS\Foundation\Exceptions;

use BoomCMS\Support\Facades\Page;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

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

        if(App::environment('production')) {
            if ($e instanceof FileNotFoundException || $e instanceof ModelNotFoundException) {
                return response()->view('boomcms::errors.404', ['title' => '404']);
            }
        }
        
        return parent::render($request, $e);
    }

    protected function prepareException(Exception $e)
    {
        if ($e instanceof FileNotFoundException || $e instanceof ModelNotFoundException) {
            return new NotFoundHttpException($e->getMessage(), $e);
        }

        return $e;
    }
}
