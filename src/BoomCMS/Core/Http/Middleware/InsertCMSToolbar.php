<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Editor\Editor;

use Illuminate\Support\Facades\View;

class InsertCMSToolbar
{
    /**
     *
     * @var Editor
     */
    protected $editor;

    public function __construct(Editor $editor)
    {
        $this->editor = $editor;
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
        if ( !$this->editor->isActive()) {
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
                'page_id' => $this->editor->getActivePage()->getId(),
            ]);

            $newHtml = str_replace($matches[0], (string) $head, $originalHtml);
            $response->setContent($newHtml);
        }

        return $response;
    }

}
