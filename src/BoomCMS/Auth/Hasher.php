<?php

namespace BoomCMS\Auth;

use Hautelook\Phpass\PasswordHash;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

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

    public function check($value, $hashedValue, array $options = [])
    {
        return $this->hasher->checkPassword($value, $hashedValue);
    }

    public function make($value, array $options = [])
    {
        return $this->hasher->HashPassword($value);
    }

    public function needsRehash($hashedValue, array $options = [])
    {
    }
}
