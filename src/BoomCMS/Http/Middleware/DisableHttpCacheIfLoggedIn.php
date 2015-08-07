<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Core\Auth\Auth;
use Closure;
use Illuminate\Http\Response;

class DisableHttpCacheIfLoggedIn
{
    /**
     * @var Auth
     */
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($this->auth->isLoggedIn() && $response instanceof Response) {
            $response->header('Cache-Control', 'no-cache, max-age=0, must-revalidate, no-store');
        }

        return $response;
    }
}
