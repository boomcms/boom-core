<?php

namespace BoomCMS\Core\Environment;

use Symfony\Component\HttpKernel\Exception\FatalErrorException;

class InvalidEnvironmentException extends FatalErrorException
{
    public function __construct($environment)
    {
        parent::__construct("Invalid environment: " . $environment);
    }
}