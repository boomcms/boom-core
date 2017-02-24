<?php

namespace BoomCMS\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

class RequireAssetVisible
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem, Guard $guard)
    {
        $this->guard = $guard;
        $this->filesystem = $filesystem;
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
        $asset = $request->route()->getParameter('asset');

        if (!$this->filesystem->exists($asset->getFilename())) {
            abort(404);
        }

        if (!$asset->isPublic() && !$this->guard->check()) {
            abort(401);
        }

        return $next($request);
    }
}
