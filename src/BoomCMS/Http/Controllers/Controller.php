<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Facades\Router;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @var string
     */
    protected $role;

    public function __construct(Request $request)
    {
        $this->request = $request;

        if ($this->role) {
            $this->authorize($this->role, Router::getActiveSite());
        }
    }
}
