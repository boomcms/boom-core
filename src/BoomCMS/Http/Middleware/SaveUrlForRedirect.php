<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SaveUrlForRedirect
{
    protected $ignore = [
        'cms/login',
        'cms/logout',
        'cms/recover',
    ];

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
        $response = $next($request);

        if ($response->isOk() &&
            !in_array($request->path(), $this->ignore) &&
            strpos($request->path(), '/asset') !== 0 &&
            strpos($request->path(), '/cms/editor/toolbar') !== 0
        ) {
            Session::put('boomcms.redirect_url', $request->path());
        }

        return $response;
    }
}
