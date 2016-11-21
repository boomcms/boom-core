<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Editor\Editor;
use BoomCMS\Support\Facades\BoomCMS;
use BoomCMS\UI;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DefineCMSViewSharedVariables
{
    private $app;

    /**
     * @var Editor
     */
    private $editor;

    public function __construct(Application $app, Editor $editor)
    {
        $this->app = $app;
        $this->editor = $editor;
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

        View::share('boomJS', "<script type='text/javascript' src='/vendor/boomcms/boom-core/js/$jsFile?".BoomCMS::getVersion()."'></script>");
        View::share('editor', $this->editor);

        return $next($request);
    }
}
