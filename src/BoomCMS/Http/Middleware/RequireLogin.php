<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->auth->check()) {
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }
}
