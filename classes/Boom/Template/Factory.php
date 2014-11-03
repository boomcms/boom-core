<?php

namespace Boom\Template;

use Model_Template;

abstract class Factory
{
    public static function byId($id)
    {
        return new Template(new Model_Template($id));
    }

    public static function byFilename($filename)
    {
        return new Template(new Model_Template(['filename' => $filename]));
    }
}
