<?php

namespace Boom\Exception\Handler;

use Kohana;
use Kohana_Exception;
use HTTP_Exception;
use Exception;
use Boom\Log\ErrorLogger;

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

    /**
     *
     * @var ErrorLogger
     */
    private $errorLogger;

    public function __construct(Exception $e)
    {
        $this->e = $e;
        $this->code = ($e instanceof HTTP_Exception) ? $e->getCode() : 500;
        $this->errorLogger = new ErrorLogger();
    }

    public function execute()
    {
        if (! $this->e instanceof HTTP_Exception) {
            $this->errorLogger->critical(Kohana_Exception::text($this->e));
        }
    }

    public static function handle(Exception $e)
    {
        try {
            $handler_class = (Kohana::$environment === Kohana::PRODUCTION || Kohana::$environment === Kohana::STAGING) ? 'Priv' : 'Pub';
            $class = "\\Boom\\Exception\\Handler\\$handler_class";
            $handler = new $class($e);

            $handler->execute();
        } catch (Exception $e) {
            Kohana_Exception::handler($e);
        }
    }

    public static function setExceptionHandler()
    {
        set_exception_handler(['\\Boom\\Exception\\Handler\\Handler', 'handle']);
    }
}
