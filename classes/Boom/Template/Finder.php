<?php

namespace Boom\Template;

use Boom;
use \Model_Template as TemplateModel;

class Finder extends Boom\Finder\Finder
{
    public function __construct()
    {
        $this->_query = new TemplateModel();
    }

    public function find()
    {
        $model = parent::find();

        return new Template($model);
    }

    public function findAll()
    {
        $templates = parent::findAll()->as_array();

        array_walk($templates, function (&$template) {
            $template = new Template($template);
        });

        return $templates;
    }
}
