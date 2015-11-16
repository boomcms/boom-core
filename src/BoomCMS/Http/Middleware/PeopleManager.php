<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Support\Facades\Group;
use Closure;
use Illuminate\Support\Facades\View;

class PeopleManager
{
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
        $response = $next($request);

        if (!$request->ajax()) {
            $v = View::make('boomcms::people.manager', [
                'groups'  => Group::findAll(),
                'content' => $response->getContent(),
            ]);

            $response->setContent($v);
        }

        return $response;
    }
}
