<?php

namespace BoomCMS\Core\Http\Middleware;

use BoomCMS\Support\Facades\Editor;
use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class DefineGlobalViewSharedVariables
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
        $editor = Editor::getFacadeRoot();

        View::share('request', $request);
        View::share('editor', $editor);

        $viewHelpers = Config::get('boomcms.viewHelpers');

        foreach ($viewHelpers as $key => $value) {
            View::share($key, $value);
        }

        return $next($request);
    }
}
