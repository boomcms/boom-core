<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Models\Template as Model;

class Finder extends Boom\Finder\Finder
{
    public function __construct()
    {
        $this->_query = new Model();
    }

    public function find()
    {
        $model = parent::find();

        return new Template($model);
    }

    public function findAll()
    {
        $templates = parent::findAll();

        array_walk($templates, function (&$template) {
            $template = new Template($template->toArray());
        });

        return $templates;
    }
}
