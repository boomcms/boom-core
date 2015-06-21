<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Finder\Finder as BaseFinder;
use BoomCMS\Core\Models\Page as Model;
use BoomCMS\Core\Page\Page;

use Illuminate\Support\Facades\App;

class Finder extends BaseFinder
{
    const TITLE = 'version.title';
    const MANUAL = 'sequence';
    const DATE = 'visible_from';
    const EDITED = 'edited_time';

    public function __construct(Editor $editor = null)
    {
        $this->editor = $editor ?: App::make('BoomCMS\Core\Editor\Editor');

        $this->query = Model::currentVersion($editor)->withUrl();

        if ( !$this->editor->isEnabled()) {
            $this->query = $this->query->isVisible();
        }
    }

    public function find()
    {
        $model = parent::find();

         return $model? new Page($model->toArray()): new Page([]);
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
