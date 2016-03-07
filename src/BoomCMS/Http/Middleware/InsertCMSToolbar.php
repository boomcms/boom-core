<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Support\Facades\Router;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class InsertCMSToolbar
{
    /**
     * @var Application
     */
    protected $app;

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
        $activePage = Router::getActivePage();

        if ($activePage === null || Gate::denies('toolbar', $activePage)) {
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
                'page_id'             => $activePage->getId(),
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
