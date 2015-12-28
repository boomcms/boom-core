<?php

namespace BoomCMS\Http\Middleware;

use Illuminate\Auth\AuthManager;
use Closure;
use Illuminate\Http\Response;

class DisableHttpCacheIfLoggedIn
{
    /**
     * @var Auth
     */
    protected $auth;

    public function __construct(AuthManager $auth)
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

        if ($this->auth->check() && $response instanceof Response) {
            $response->header('Cache-Control', 'no-cache, max-age=0, must-revalidate, no-store');
        }

        return $response;
    }
}
