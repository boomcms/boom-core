<?php

namespace BoomCMS\Auth;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Hautelook\Phpass\PasswordHash;

class Hasher implements HasherContract
{
    /**
     * @var PasswordHash
     */
    protected $hasher;

    public function __construct()
    {
        $this->hasher = new PasswordHash(8, false);
    }

    public function check($value, $hashedValue, array $options = array())
    {
        return $this->hasher->checkPassword($value, $hashedValue);
    }

    public function make($value, array $options = array())
    {
        return $this->hasher->HashPassword($value);
    }

    public function needsRehash($hashedValue, array $options = array())
    {

    }
}
