<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Support\Facades\Editor;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class InsertCMSToolbar
{
    /**
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
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $activePage = Editor::getActivePage();

        if ($activePage === null || !Auth::check('edit', $activePage)) {
            return $next($request);
        }

        $response = $next($request);

        $originalHtml = ($response instanceof Response)
            ? $response->getOriginalContent()
            : (string) $response;

        preg_match('|(.*)(</head>)(.*<body[^>]*>)|imsU', $originalHtml, $matches);

        if (!empty($matches)) {
            $head = view('boomcms::editor.iframe', [
                'before_closing_head' => $matches[1],
                'body_tag'            => $matches[3],
                'editor'              => $this->app['boomcms.editor'],
                'page_id'             => $this->app['boomcms.editor']->getActivePage()->getId(),
            ]);

            $newHtml = str_replace($matches[0], (string) $head, $originalHtml);

            if ($response instanceof Response) {
                $response->setContent($newHtml);
            } else {
                return $newHtml;
            }
        }

        return $response;
    }
}
