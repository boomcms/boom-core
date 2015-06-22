<?php

namespace BoomCMS\Core\Controllers;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Environment\Environment;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager as Session;

use \View as View;

class Controller extends BaseController
{
    /**
     * The current user.
     *
     * @var Boom\Person\Person
     */
    public $person;

    /**
     * @var Auth
     */
    public $auth;

    /**
     *
     * @var Boom
     */
    public $boom;

    /**
     * @var	Editor
     */
    public $editor;

    /**
     * @var Session
     */
    public $session;

    /**
     *
     * @var View
     */
    public $template;

    public function __construct(Request $request, Session $session, Auth $auth, Editor $editor)
    {
        $this->session = $session;
        $this->request = $request;
        $this->auth = $auth;
        $this->editor = $editor;
        $this->person = $this->auth->getPerson();
    }

    /**
	 * Checks whether the current user is authorized to perform a particular action.
     *
	 * @uses	Auth::isLoggedIn()
	 * @param string $role
	 * @param Model_Page $page
	 */
    public function authorization($role, Page $page = null)
    {
        if ( ! $this->auth->isLoggedIn()) {
            abort(401);
        }

        if ( ! $this->auth->loggedIn($role, $page)) {
           abort(403);
        }
    }
}
