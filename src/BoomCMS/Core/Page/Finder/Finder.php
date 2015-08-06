<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Support\Facades\Editor;
use BoomCMS\Core\Finder\Finder as BaseFinder;
use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Core\Page\Page;

class Finder extends BaseFinder
{
    const TITLE = 'version.title';
    const MANUAL = 'sequence';
    const DATE = 'visible_from';
    const EDITED = 'edited_time';

    public function __construct()
    {
        $this->query = Model::currentVersion()->withUrl();

        if (Editor::isDisabled()) {
            $this->query = $this->query->isVisible();
        }
    }

    public function find()
    {
        $model = parent::find();

         return $model ? new Page($model->toArray()) : new Page([]);
    }

    public function findAll()
    {
        $pages = parent::findAll();
        $return = [];

        foreach ($pages as $page) {
            $return[] = new Page($page->toArray());
        }

        return $return;
    }
}