<?php

namespace Boom\Environment;

class Development extends Environment
{
    public function isDevelopment()
    {
        return true;
    }
}