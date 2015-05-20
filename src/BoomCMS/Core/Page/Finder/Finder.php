<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Model\Page as Model;
use BoomCMS\Core\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    const TITLE = 'version.title';
    const MANUAL = 'sequence';
    const DATE = 'visible_from';
    const EDITED = 'edited_time';

    public function __construct(Editor $editor)
    {
        $editor = $editor ?: Editor::instance();

        $this->query = Model::currentVersion()
            ->withUrl()
            ->isVisible();

        if ($editor->isDisabled()) {
            $this->query = $this->query->isVisible();
        }
    }

    public function find()
    {
        $model = parent::find();

        return new Page($model);
    }

    public function findAll()
    {
        $pages = parent::findAll()->as_array();

        array_walk($pages, function (&$page) {
           $page = new Page($page);
        });

        return $pages;
    }
}
