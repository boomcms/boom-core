<?php

namespace BoomCMS\Core\Editor;

use BoomCMS\Core\Auth\Auth;
use Illuminate\Session\SessionManager as Session;

class Editor
{
    const EDIT = 1;
    const DISABLED = 2;
    const PREVIEW = 3;

    public static $default = Editor::EDIT;

    /**
	 *
	 * @var	Auth
	 */
    protected $auth;

    protected $session;
    protected $state;
    protected $statePersistenceKey = 'editor_state';

    public function __construct(Auth $auth, Session $session)
    {
        $this->auth = $auth;
        $this->session = $session;

        $default = ($this->auth->isLoggedIn()) ? static::$default : static::DISABLED;
        $this->state = $this->session->get($this->statePersistenceKey, $default);
    }

    public function enable()
    {
        return $this->setState(static::EDIT);
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

    public function setState($state)
    {
        $this->state = $state;

        return $this->session->put($this->statePersistenceKey, $state);
    }
}
