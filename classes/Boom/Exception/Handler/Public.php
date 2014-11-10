<?php

/**
 * Exception handler which outputs debugging information
 */
class Boom_Boom_Exception_Handler_Public extends Boom_Exception_Handler
{
    public function execute()
    {
        parent::execute();

        echo Kohana_Exception::response($this->e)->send_headers()->body();
        exit(1);
    }
}
