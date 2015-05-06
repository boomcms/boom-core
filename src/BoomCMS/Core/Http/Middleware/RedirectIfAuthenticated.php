<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Auth\Auth;
use Illuminate\Http\RedirectResponse;

class RedirectIfAuthenticated
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
        if ($this->auth->isLoggedIn()) {
            return new RedirectResponse(url('/'));
        }

        return $next($request);
    }

}
