<?php

namespace BoomCMS\Editor;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class Editor
{
    const EDIT = 1;
    const DISABLED = 2;
    const PREVIEW = 3;
    const HISTORY = 4;

    public static $default = self::EDIT;

    /**
     * @var Page
     */
    protected $activePage;

    protected $state;
    protected $statePersistenceKey = 'editor_state';
    protected $timePersistenceKey = 'editor_time';

    protected $validStates = [
        self::EDIT,
        self::DISABLED,
        self::PREVIEW,
        self::HISTORY,
    ];

    public function __construct()
    {
        $this->state = (Auth::check()) ?
            Session::get($this->statePersistenceKey, static::$default)
            : static::DISABLED;
    }

    /**
     * Disable the editor.
     *
     * @return $this
     */
    public function disable()
    {
        return $this->setState(static::DISABLED);
    }

    /**
     * Enable the editor.
     *
     * @return $this
     */
    public function enable()
    {
        $this->setTime(null);

        return $this->setState(static::EDIT);
    }

    /**
     * Get the time to view pages at.
     *
     * @return DateTime
     */
    public function getTime()
    {
        $timestamp = Session::get($this->timePersistenceKey, time());

        return (new DateTime())->setTimestamp($timestamp);
    }

    /**
     * Returns true if the editor is disabled.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->hasState(static::DISABLED);
    }

    /**
     * Returns true if the editor is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->hasState(static::EDIT);
    }

    /**
     * Returns true if the editor is viewing the site as it existed in the past.
     *
     * @return bool
     */
    public function isHistory()
    {
        return $this->hasState(static::HISTORY);
    }

    /**
     * Returns true if the editor has the given state.
     *
     * @param int $state
     *
     * @return bool
     */
    public function hasState($state)
    {
        return $this->state === (int) $state;
    }

    /**
     * Returns the current state of the editor.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Put the editor into preview mode.
     *
     * @return $this
     */
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

    /**
     * Set a time at which to view pages at.
     *
     * @param DateTime $time
     *
     * @return $this
     */
    public function setTime(DateTime $time = null)
    {
        $timestamp = $time ? $time->getTimestamp() : null;

        Session::put($this->timePersistenceKey, $timestamp);

        if ($time) {
            $this->setState(static::HISTORY);
        }

        return $this;
    }
}
