<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Auth\Auth;
use Illuminate\Http\RedirectResponse;

class RequireLogin
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! $this->auth->isLoggedIn())
        {
            // TODO: check HTTP response code - needs to be 401.
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }

}