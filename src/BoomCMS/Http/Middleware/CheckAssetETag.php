<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAssetETag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $asset = $request->route()->getParameter('asset');
        $etag = $asset->getLastModified()->getTimestamp();

        if ($request->header('If-None-Match') == $etag) {
            abort(304)->header('etag', $etag);
        }

        $response = $next($request);

        return $response
            ->header('Cache-Control', 'public, max-age=100800, must-revalidate')
            ->header('etag', $etag);
    }

}
