<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Repositories\Asset;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class RequireAssetVisible
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var Asset
     */
    protected $repository;

    public function __construct(Asset $repository, Guard $guard)
    {
        $this->guard = $guard;
        $this->repository = $repository;
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

        if (!$this->repository->exists($asset)) {
            abort(404);
        }

        if (!$asset->isPublic() && !$this->guard->check()) {
            abort(401);
        }

        return $next($request);
    }
}
