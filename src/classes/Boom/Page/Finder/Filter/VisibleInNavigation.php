<?php

namespace Boom\Page\Finder\Filter;

use \Boom\Editor\Editor as Editor;

class VisibleInNavigation extends \Boom\Finder\Filter
{
    /**
	 *
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

    public function execute(\ORM $query)
    {
        return $query->where($this->_getNavigationVisibilityColumn(), '=', true);
    }
}
