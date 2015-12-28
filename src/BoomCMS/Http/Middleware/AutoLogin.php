<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthMananger;
use Illuminate\Http\Request;

class AutoLogin
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
    public function handle(Request $request, Closure $next)
    {
        if ($request->cookie($this->auth->getAutoLoginCookie())) {
            $this->auth->autoLogin($request);
        }

        return $next($request);
    }
}
