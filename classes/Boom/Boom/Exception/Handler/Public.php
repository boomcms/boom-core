<?php

/**
 * Exception handler which outputs debugging information
 */
class Boom_Boom_Exception_Handler_Public extends Boom_Exception_Handler
{
	public function execute()
	{
		Kohana_Exception::handler($this->_e);
	}
}