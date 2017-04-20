<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class RequireAssetVisible
{
    /**
     * @var Guard
     */
    protected $guard;

    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
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
        $asset = $request->route()->parameter('asset');

        if (!$asset->isPublic() && !$this->guard->check()) {
            abort(401);
        }

        return $next($request);
    }
}
