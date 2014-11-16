<?php

namespace Boom\Environment;

class Staging extends Environment
{
    /**
     *
     * @return boolean
     */
    public function isStaging()
    {
        return true;
    }
}