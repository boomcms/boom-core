<?php

namespace Boom\Page;

use Boom;
use Boom\Editor\Editor;

class Finder extends Boom\Finder\Finder
{
    const TITLE = 'version.title';
    const MANUAL = 'sequence';
    const DATE = 'visible_from';
    const EDITED = 'edited_time';

    public function __construct(Editor $editor = null)
    {
        $editor = $editor ?: Editor::instance();

        $this->_query = \ORM::factory('Page')
            ->where('deleted', '=', false)
            ->with_current_version($editor)
            ->where('page.primary_uri', '!=', null);
    }

    public function find()
    {
        $model = parent::find();

        return new Page($model);
    }

    public function findAll()
    {
        $pages = parent::findAll()->as_array();

        return new \Boom\ArrayCallbackIterator($pages, function ($page) {
            return $page instanceof \Boom\Page ? $page : new Page($page);
        });
    }
}
