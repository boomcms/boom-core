<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Core\Auth\Auth;
use Closure;
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
        if (!$this->auth->isLoggedIn()) {
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }
}
