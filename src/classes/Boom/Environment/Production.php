<?php

namespace Boom\Environment;

class Production extends Environment
{
    /**
     *
     * @return boolean
     */
    public function isProduction()
    {
        return true;
    }
}
