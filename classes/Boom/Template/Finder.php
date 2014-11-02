<?php

namespace Boom\Template;

use \Model_Template as TemplateModel;

class Finder extends \Boom\Finder
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

        return new \Boom\ArrayCallbackIterator($templates, function ($template) {
            return new Template($template);
        });
    }
}
