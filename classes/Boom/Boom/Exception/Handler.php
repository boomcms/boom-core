<?php

abstract class Boom_Boom_Exception_Handler
{
	/**
	 * HTTP response code for this type of exception
	 * 
	 * @var int
	 */
	protected $_code;

	/**
	 *
	 * @var Exception
	 */
	protected $_e;

	public function __construct(Exception $e)
	{
		$this->_e = $e;
		$this->_code = ($e instanceof HTTP_Exception) ? $e->getCode() : 500;
	}

	public static function handle(Exception $e)
	{
		try
		{
			$handler_class = (Kohana::$environment === Kohana::PRODUCTION OR Kohana::$environment === Kohana::STAGING)? 'Boom_Exception_Handler_Private' : 'Boom_Exception_Handler_Public';
			$handler = new $handler_class($e);

			$handler->execute();
		}
		catch (Exception $e)
		{
			Kohana_Exception::handler($e);
		}
	}

	public static function set_exception_handler()
	{
		set_exception_handler(array('Boom_Exception_Handler', 'handle'));
	}
}