<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Environment;
use Illuminate\Http\RedirectResponse;

class RequireLoginForDevelopmentSites
{
    /**
     *
     * @var Environment
     */
    protected $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
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
        if ($this->environment->requiresLogin())
        {
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }

}