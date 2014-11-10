<?php

namespace Boom\Exception\Handler;

use Kohana;
use Kohana_Exception;
use HTTP_Exception;
use Exception;

abstract class Handler
{
    /**
	 * HTTP response code for this type of exception
	 *
	 * @var int
	 */
    protected $code;

    /**
	 *
	 * @var Exception
	 */
    protected $e;

    public function __construct(Exception $e)
    {
        $this->e = $e;
        $this->code = ($e instanceof HTTP_Exception) ? $e->getCode() : 500;
    }

    public function execute()
    {
        $this->_logException();
    }

    public static function handle(Exception $e)
    {
        try {
            $handler_class = (Kohana::$environment === Kohana::PRODUCTION || Kohana::$environment === Kohana::STAGING) ? 'Priv' : 'Pub';
            $handler = new $handler_class($e);

            $handler->execute();
        } catch (Exception $e) {
            Kohana_Exception::handler($e);
        }
    }

    protected function _logException()
    {
        Kohana_Exception::log($this->e);
    }

    public static function setExceptionHandler()
    {
        set_exception_handler(['\\Boom\\Exception\\Handler\\Handler', 'handle']);
    }
}
