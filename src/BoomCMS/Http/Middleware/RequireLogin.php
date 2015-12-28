<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Auth\Guard as Auth;
use Illuminate\Http\RedirectResponse;

class RequireLogin
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
        if (!$this->auth->check()) {
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }
}
