<?php

namespace Boom\Exception\Handler;

use Kohana_Exception;

class Pub extends Handler
{
    public function execute()
    {
        parent::execute();

        echo Kohana_Exception::response($this->e)->send_headers()->body();
        exit(1);
    }
}
