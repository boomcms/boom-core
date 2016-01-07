<?php

namespace BoomCMS\Editor;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

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

    protected $state;
    protected $statePersistenceKey = 'editor_state';

    protected $validStates = [
        self::EDIT,
        self::DISABLED,
        self::PREVIEW,
    ];

    public function __construct()
    {
        $this->state = (Auth::check()) ?
            Session::get($this->statePersistenceKey, static::$default)
            : static::DISABLED;
    }

    public function disable()
    {
        return $this->setState(static::DISABLED);
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
        return $this->hasState(static::EDIT);
    }

    public function hasState($state)
    {
        return $this->state == $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function preview()
    {
        return $this->setState(static::PREVIEW);
    }

    /**
     * @param int $state
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setState($state)
    {
        if (!in_array($state, $this->validStates)) {
            throw new InvalidArgumentException("Invalid editor state: $state");
        }

        $this->state = $state;

        Session::put($this->statePersistenceKey, $state);

        return $this;
    }
}
