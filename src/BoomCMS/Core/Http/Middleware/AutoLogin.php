<?php

namespace BoomCMS\Core\Http\Middleware;

use BoomCMS\Core\Auth\Auth;

use Closure;
use Illuminate\Http\Request;

class AutoLogin
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
    public function handle(Request $request, Closure $next)
    {
        if ($request->cookie($this->auth->getAutoLoginCookie())) {
            $this->auth->autoLogin($request);
        }

        return $next($request);
    }
}
