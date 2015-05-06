<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Auth\Auth;

class DisableHttpCacheIfLoggedIn
{
    /**
     *
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
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($this->auth->isLoggedIn()) {
            $response->header('Cache-Control', 'private');
        }

        return $response;
    }

}
