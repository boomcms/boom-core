<?php

namespace BoomCMS\Editor;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Database\Models\Page as PageObject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Editor
{
    const EDIT = 1;
    const DISABLED = 2;
    const PREVIEW = 3;

    public static $default = self::EDIT;

    /**
     * @var Page
     */
    protected $activePage;

    /**
     * @var bool
     */
    protected $loggedIn;

    protected $state;
    protected $statePersistenceKey = 'editor_state';

    public function __construct()
    {
        $this->loggedIn = Auth::check();

        if ($this->loggedIn === true) {
            $this->state = Session::get($this->statePersistenceKey, static::$default);
        } else {
            $this->state = static::DISABLED;
        }
    }

    public function disable()
    {
        return $this->setState(static::DISABLED);
    }

    public function enable()
    {
        return $this->setState(static::EDIT);
    }

    /**
     * Whether the editor is active.
     *
     * Determines whether the CMS toolbar should be injected into the response HTML.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getActivePage()
            && $this->loggedIn
            && Auth::check('edit', $this->getActivePage());
    }

    public function isDisabled()
    {
        return $this->hasState(static::DISABLED);
    }

    public function isEnabled()
    {
        return $this->hasState(static::EDIT);
    }

    public function hasState($state)
    {
        return $this->state == $state;
    }

    public function getActivePage()
    {
        return $this->activePage ?: new PageObject();
    }

    public function getState()
    {
        return $this->state;
    }

    public function preview()
    {
        return $this->setState(static::PREVIEW);
    }

    public function setActivePage(Page $page)
    {
        $this->activePage = $page;

        return $this;
    }

    public function setState($state)
    {
        $this->state = $state;

        return Session::put($this->statePersistenceKey, $state);
    }
}
