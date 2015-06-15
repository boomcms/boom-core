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
        $this->environment = $environment;
        $this->session = $session;
        $this->request = $request;
        $this->auth = $auth;
        $this->editor = $editor;
        $this->person = $this->auth->getPerson();
    }

    /**
	 * Checks whether the current user is authorized to perform a particular action.
	 *
	 * Throws a HTTP_Exception_403 error if the user hasn't been given the required role.
	 *
	 * @uses	Auth::isLoggedIn()
	 * @param string $role
	 * @param Model_Page $page
	 */
    public function authorization($role, Page $page = null)
    {
        if ( ! $this->auth->isLoggedIn()) {
            throw new \HTTP_Exception_401();
        }

        if ( ! $this->auth->loggedIn($role, $page)) {
            throw new \HTTP_Exception_403();
        }
    }

    /**
	 * Log an action in the CMS log
	 *
	 * @param string $activity
	 */
    public function log($activity)
    {
        // Add an item to the log table with the relevant details
//        ORM::factory('Log')
//            ->values([
//                'ip'            =>    Request::$client_ip,
//                'activity'        =>    $activity,
//                'person_id'    =>    $this->person->getId(),
//            ])
//            ->create();
    }

    public function after()
    {
        if ($this->template instanceof View && ! $this->response->body()) {
            parent::after();

            View::bind_global('person', $this->person);
            View::bind_global('auth', $this->auth);

            $this->response->body($this->template);
        }
    }
}
