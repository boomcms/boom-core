<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Database\Models\Page;
use BoomCMS\Routing\Router;
use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckPageAcl
{
    /**
     * @var Gate
     */
    protected $gate;

    /**
     *
     * @var Guard
     */
    protected $guard;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @param Router $router
     * @param Gate   $gate
     */
    public function __construct(Router $router, Guard $guard, Gate $gate)
    {
        $this->page = $router->getActivePage();
        $this->gate = $gate;
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
        if ($this->page->aclEnabled()) {
            if (!$this->guard->check()) {
                return new RedirectResponse(route('login'));
            }

            if ($this->gate->denies('view', $this->page)) {
                abort(403);
            }
        }

        return $next($request);
    }
}
