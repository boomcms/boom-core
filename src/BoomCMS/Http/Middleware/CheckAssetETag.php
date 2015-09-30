<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheckAssetETag
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
        $asset = $request->route()->getParameter('asset');

        if (!$asset) {
            return $next($request);
        }

        $etag = $asset->getLastModified()->getTimestamp();

        if ($request->header('If-None-Match') == $etag) {
            abort(304)->header('etag', $etag);
        }

        $response = $next($request);

        if ($response instanceof StreamedResponse) {
            return $response;
        }

        return $response
            ->header('Cache-Control', 'public, must-revalidate')
            ->header('etag', $etag);
    }
}
