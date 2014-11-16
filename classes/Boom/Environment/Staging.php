<?php

namespace Boom\Environment;

class Staging extends Environment
{
    public function isStaging()
    {
        return true;
    }
}