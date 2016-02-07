<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Database\Models\Page as Model;
use BoomCMS\Foundation\Finder\Finder as BaseFinder;
use BoomCMS\Support\Facades\Editor;

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
            $this->query = $this->query->isVisibleAtTime(time());
        }
    }
}
