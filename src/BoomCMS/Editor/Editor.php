<?php

namespace BoomCMS\Editor;

use DateTime;
use Illuminate\Session\Store;
use InvalidArgumentException;

class Editor
{
    const EDIT = 1;
    const DISABLED = 2;
    const PREVIEW = 3;
    const HISTORY = 4;

    /**
     * @var SessionManager
     */
    protected $session;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var string
     */
    protected $statePersistenceKey = 'editor_state';

    /**
     * @var string
     */
    protected $timePersistenceKey = 'editor_time';

    /**
     * @var array
     */
    protected $validStates = [
        self::EDIT,
        self::DISABLED,
        self::PREVIEW,
        self::HISTORY,
    ];

    /**
     * @param Store $session
     * @param int $default
     */
    public function __construct(Store $session, $default = self::DISABLED)
    {
        $this->session = $session;
        $this->state = $session->get($this->statePersistenceKey, $default);
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
        return $this->setState(static::EDIT);
    }

    /**
     * Get the time to view pages at.
     *
     * @return DateTime
     */
    public function getTime()
    {
        // Time should only be used with the history state.
        if (!$this->isHistory()) {
            return new DateTime('now');
        }

        $timestamp = $this->session->get($this->timePersistenceKey, time());

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

        $this->session->put($this->statePersistenceKey, $state);

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

        $this->session->put($this->timePersistenceKey, $timestamp);

        if ($time) {
            $this->setState(static::HISTORY);
        }

        return $this;
    }
}
