<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Environment\Environment;

use Illuminate\Support\Facades\View;

class InsertCMSToolbar
{
    /**
     *
     * @var Auth
     */
    protected $auth;

    /**
     *
     * @var Editor
     */
    protected $editor;

    /**
     *
     * @var Environment
     */
    protected $environment;

    public function __construct(Auth $auth, Editor $editor, Environment $environment)
    {
        $this->auth = $auth;
        $this->editor = $editor;
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
        if ( !$this->auth->loggedIn()) {
            return $next($request);
        }

        $response = $next($request);

        $originalHtml = $response->getOriginalContent();
        preg_match("|(.*)(</head>)(.*<body[^>]*>)|imsU", $originalHtml, $matches);

        if ( ! empty($matches)) {
            $head = View::make('boom::editor.iframe', [
                'before_closing_head' => $matches[1],
                'body_tag' => $matches[3],
                'editor' => $this->editor,
                'page_id' => $request->route()->getParameter('boomcms.currentPage')->getId(),
            ]);

            $newHtml = str_replace($matches[0], (string) $head, $originalHtml);
            $response->setContent($newHtml);
        }

        return $response;
    }

}
