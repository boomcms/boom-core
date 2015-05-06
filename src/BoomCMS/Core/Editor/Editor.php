<?php

namespace BoomCMS\Core\Editor;

use BoomCMS\Core\Auth\Auth;
use Illuminate\Session\SessionManager as Session;

class Editor
{
    const EDIT = 1;
    const DISABLED = 2;
    const PREVIEW = 3;

    public static $default = Editor::PREVIEW;
    public static $instance;

    /**
	 *
	 * @var	Auth
	 */
    protected $auth;

    protected $liveTime;
    protected $liveTimePersistenceKey = 'editor_liveTime';
    protected $session;
    protected $state;
    protected $statePersistenceKey = 'editor_state';

    public function __construct(Auth $auth, Session $session)
    {
        $this->auth = $auth;
        $this->session = $session;

        // Determine the default value to pass to Session::get()
        // If the user is logged in then the default is preview, if they're not logged in then it should be disabled.
        $default = ($this->auth->isLoggedIn()) ? static::$default : static::DISABLED;
        $this->state = $this->session->get($this->statePersistenceKey, $default);
    }

    /**
	 *
	 * @return Editor
	 */
    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static(new Auth(Session::instance()), Session::instance());
        }

        return static::$instance;
    }

    public function isDisabled()
    {
        return $this->hasState(static::DISABLED);
    }

    public function isEnabled()
    {
        return $this->auth->loggedIn() && $this->hasState(static::EDIT);
    }

    public function hasState($state)
    {
        return ($this->state == $state);
    }

    public function getState()
    {
        return $this->state;
    }

    public function getLiveTime()
    {
        if ($this->liveTime === null) {
            $this->liveTime = $this->session->read($this->liveTimePersistenceKey, time());
        }

        return $this->liveTime;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this->session->put($this->statePersistenceKey, $state);
    }

    /**
	 * The time to use for viewing live pages.
	 * This allows for viewing pages as they were at a certain time in the page.
	 * If no time has been set then the value of $_SERVER['REQUEST_TIME'] is used.
	 *
	 */
    public function setLiveTime($time = null)
    {
        return $this->session>write($this->liveTimePersistenceKey, $time);
    }
}
