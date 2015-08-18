<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Editor\Editor as Editor;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class VisibleInNavigation extends Filter
{
    /**
     * @var Editor
     */
    protected $_editor;

    public function __construct(Editor $editor = null)
    {
        $this->_editor = $editor ?: Editor::instance();
    }

    protected function _getNavigationVisibilityColumn()
    {
        return ($this->_editor->isEnabled()) ? 'visible_in_nav_cms' : 'visible_in_nav';
    }

    public function build(Builder $query)
    {
        return $query->where($this->_getNavigationVisibilityColumn(), '=', true);
    }
}
