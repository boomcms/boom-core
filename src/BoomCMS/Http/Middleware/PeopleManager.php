<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Core\Group;
use Closure;
use Illuminate\Support\Facades\View;

class PeopleManager
{
    /**
     * @var Group\Provider
     */
    protected $provider;

    public function __construct(Group\Provider $provider)
    {
        $this->provider = $provider;
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
        $response = $next($request);

        if (!$request->ajax()) {
            $v = View::make('boom::people.manager', [
                'groups'  => $this->provider->findAll(),
                'content' => $response->getContent(),
            ]);

            $response->setContent($v);
        }

        return $response;
    }
}
