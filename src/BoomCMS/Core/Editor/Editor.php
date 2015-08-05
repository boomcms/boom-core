<?php

namespace BoomCMS\Core\Editor;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page\Page;
use Illuminate\Session\SessionManager as Session;

class Editor
{
    const EDIT = 1;
    const DISABLED = 2;
    const PREVIEW = 3;

    public static $default = Editor::EDIT;

    /**
     *
     * @var Auth
     */
    protected $auth;

    /**
     *
     * @var Page
     */
    protected $activePage;

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
     * @return boolean
     */
    public function isActive()
    {
        return $this->getActivePage()->loaded() && $this->auth->loggedIn('edit_page', $this->getActivePage());
    }

    public function isDisabled()
    {
        return $this->hasState(static::DISABLED);
    }
    
    /**
     * Returns whether or not the logged in user can edit the content of a page.
     * 
     * A page can be edited if it was created by a user or they have edit permissions for the page.
     * 
     * @param Page $page
     * @return boolean
     */
    public function isEditable(Page $page)
    {
        return $page->wasCreatedBy($this->auth->getPerson()) || $this->auth->loggedIn("edit_page_content", $page);
    }

    public function isEnabled()
    {
        return $this->hasState(static::EDIT);
    }

    public function hasState($state)
    {
        return ($this->state == $state);
    }

    public function getActivePage()
    {
        return $this->activePage ?: new Page([]);
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

        return $this->session->put($this->statePersistenceKey, $state);
    }
}
