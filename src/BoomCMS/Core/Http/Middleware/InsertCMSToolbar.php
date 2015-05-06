<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Environment;

class InsertCMSToolbar
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
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $originalHtml = $response->getOriginalContent();
        preg_match("|(.*)(</head>)(.*<body[^>]*>)|imsU", $originalHtml, $matches);

        if ( ! empty($matches)) {
            $head = new \View('boom/editor/iframe', [
                'before_closing_head' => $matches[1],
                'body_tag'    =>    $matches[3],
                'page_id'    =>    $request->route()->getParameter('boom.currentPage')->getId(),
            ]);

            $newHtml = str_replace($matches[0], $head->render(), $originalHtml);
            $response->setContent($newHtml);
        }

        return $response;
    }

}
