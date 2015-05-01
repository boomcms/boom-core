<?php

namespace BoomCMS\Core\Controller;

use BoomCMS\Core\Boom as BoomCore;
use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Editor\Editor;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager as Session;

use \View as View;

class Controller extends BaseController
{
    /**
     * The correct content-type header for JSON responses is application/json (http://stackoverflow.com/questions/477816/what-is-the-correct-json-content-type)
     *
     * Unfortunately though IE exists, and IE9 doesn't recognise the application/json type presenting the user with a download confirmation.
     *
     * We therefore need to use text/plain for JSON responses until we stop supporting broken browsers (http://stackoverflow.com/questions/13943439/json-response-download-in-ie710)
     */
    const JSON_RESPONSE_MIME = 'text/plain';

    /**
     * The current user.
     *
     * @var Boom\Person\Person
     */
    public $person;

    /**
     * @var		Auth
     */
    public $auth;

    /**
     *
     * @var Boom\Environment\Environment
     */
    public $environment;

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

    public function __construct(Request $request, Session $session)
    {
        $this->boom = BoomCore::instance();
        $this->environment = $this->boom->getEnvironment();
        $this->session = $session;
        $this->request = $request;
        $this->auth = new Auth($this->session);

        // Require the user to be logged in if the site isn't live.
        if ($this->boom->getEnvironment()->requiresLogin() && ! $this->auth->loggedIn()) {
            throw new \HTTP_Exception_401();
        }

        $this->person = $this->auth->getPerson();
        $this->editor = new Editor($this->auth, $this->session);
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
        ORM::factory('Log')
            ->values([
                'ip'            =>    Request::$client_ip,
                'activity'        =>    $activity,
                'person_id'    =>    $this->person->getId(),
            ])
            ->create();
    }

    public function after()
    {
        if ($this->template instanceof View && ! $this->response->body()) {
            parent::after();

            View::bind_global('person', $this->person);
            View::bind_global('auth', $this->auth);

            $this->response->body($this->template);
        }

        if ($this->auth->isLoggedIn()) {
            $this->response->headers('Cache-Control', 'private');
        }
    }
}
