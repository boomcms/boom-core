<?php

namespace BoomCMS\Http\Middleware;

use Closure;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\View;

class InsertCMSToolbar
{
    /**
     *
     * @var Application
     */
    protected $ap;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
        if ( !$this->app['boomcms.editor']->isActive()) {
            return $next($request);
        }

        $response = $next($request);

        $originalHtml = $response->getOriginalContent();
        preg_match("|(.*)(</head>)(.*<body[^>]*>)|imsU", $originalHtml, $matches);

        if ( ! empty($matches)) {
            $head = View::make('boom::editor.iframe', [
                'before_closing_head' => $matches[1],
                'body_tag' => $matches[3],
                'editor' => $this->app['boomcms.editor'],
                'page_id' => $this->app['boomcms.editor']->getActivePage()->getId(),
            ]);

            $newHtml = str_replace($matches[0], (string) $head, $originalHtml);
            $response->setContent($newHtml);
        }

        return $response;
    }

}
