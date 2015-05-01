<?php

namespace BoomCMS\Core\Environment;

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
