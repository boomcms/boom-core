<?php

namespace Boom\Log;

use Psr\Log\AbstractLogger;
use Kohana;
use Kohana_Log;

class ErrorLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        Kohana::$log->add(Kohana_Log::CRITICAL, (string) $message);
        Kohana::$log->write();
    }
}
