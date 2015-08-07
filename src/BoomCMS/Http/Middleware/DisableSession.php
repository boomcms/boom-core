<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DisableSession
{
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
        Config::set('session.driver', 'array');
        Config::set('cookie.driver', 'array');

        return $next($request);
    }
}
