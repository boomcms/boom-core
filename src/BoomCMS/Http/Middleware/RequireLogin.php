<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


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
            Session::put('url.intended', $request->path());
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }
}
