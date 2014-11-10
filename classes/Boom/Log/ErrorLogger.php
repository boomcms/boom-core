<?php

namespace Boom\Log;

use Psr\Log\AbstractLogger;
use Kohana;

class ErrorLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        Kohana::$log->add($level, $message);
        Kohana::$log->write();
    }
}