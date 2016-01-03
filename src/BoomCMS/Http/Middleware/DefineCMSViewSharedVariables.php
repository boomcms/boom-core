<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\UI;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DefineCMSViewSharedVariables
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
        View::share('button', function ($type, $text, $attrs = []) {
            return new UI\Button($type, $text, $attrs);
        });

        View::share('menu', function () {
            return view('boomcms::menu')->render();
        });

        View::share('menuButton', function () {
            return new UI\MenuButton();
        });

        $jsFile = $this->app->environment('local') ? 'cms.js' : 'cms.min.js';

        View::share('boomJS', "<script type='text/javascript' src='/vendor/boomcms/boom-core/js/$jsFile'></script>");
        View::share('editor', $this->app['boomcms.editor']);

        return $next($request);
    }
}
