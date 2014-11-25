<?php

use Boom\Auth\Auth;
use \Session;

class Controller_Cms_Auth extends Controller
{
    /**
	 *
	 * @var Boom\Auth\Auth
	 */
    public $auth;

    /**
	 *
	 * @var integer
	 */
    public $method;

    public function before()
    {
        $this->auth = new Auth(Session::instance());
    }

    protected function _log_login_success()
    {
        $this->_log_action(Model_AuthLog::LOGIN);
    }

    protected function _log_login_failure()
    {
        $this->_log_action(Model_AuthLog::FAILURE);
    }

    protected function _log_logout()
    {
        $this->_log_action(Model_AuthLog::LOGOUT);
    }

    private function _log_action($action)
    {
        ORM::factory('AuthLog')
            ->values([
                'person_id' => $this->auth->getPerson()->getId(),
                'action' => $action,
                'method' => $this->method,
                'ip' => ip2long(Request::$client_ip),
                'user_agent' => Request::$user_agent,
            ])
            ->create();
    }

    protected function _display_login_form($vars = [])
    {
        $vars['request'] = $this->request;

        $this->response->body(new View('boom/account/login', $vars));
    }

    protected function _get_redirect_url()
    {
        $logger = new \Boom\RedirectUrlPersister(Session::instance());
        $url = $logger->getUrl();

        if (! $url || $url == '/cms/logout') {
            $url = '/';
        }

        return $url;
    }
}
