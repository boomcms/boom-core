<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Environment\Environment;
use Illuminate\Http\RedirectResponse;

class RequireLoginForDevelopmentSites
{
    /**
     *
     * @var Environment
     */
    protected $environment;

    protected $ignoreUrls = [
        'cms/login',
        'cms/logout',
        'cms/recover',
    ];

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->environment->requiresLogin()
            && !in_array($request->path(), $this->ignoreUrls)
        ) {
            // TODO: return a 401 response with a custom error page to handle the redirect.
            return new RedirectResponse(route('login'));
        }

        return $next($request);
    }

}
