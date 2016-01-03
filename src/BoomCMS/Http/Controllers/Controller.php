<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Editor\Editor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Session\SessionManager as Session;

class Controller extends BaseController
{
    use AuthorizesRequests;

    /**
     * @var Editor
     */
    public $editor;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var Session
     */
    public $session;

    public function __construct(Request $request, Session $session, Editor $editor)
    {
        $this->session = $session;
        $this->request = $request;
        $this->editor = $editor;

        if ($this->role) {
            $this->authorize($this->role, $request);
        }
    }
}
