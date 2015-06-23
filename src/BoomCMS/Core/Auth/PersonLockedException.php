<?php

namespace BoomCMS\Core\Auth;

class PersonLockedException extends \UnexpectedValueException
{
    protected $lockedUntil;

    public function __construct($lockedUntil)
    {
        $this->lockedUntil = $lockedUntil;
    }

    public function getLockWait()
    {
        return ceil(($this->lockedUntil - time()) / 60);
    }
}