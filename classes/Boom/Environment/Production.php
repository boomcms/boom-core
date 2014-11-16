<?php

namespace Boom\Environment;

class Production extends Environment
{
    public function isProduction()
    {
        return true;
    }
}