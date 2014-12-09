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

    public static function fromModel(Model_Template $template)
    {
        $className = "Boom\\Template\\" . ucfirst($template->filename);

        return (class_exists($className))? new $className($template) : new Template($template);
    }
}
