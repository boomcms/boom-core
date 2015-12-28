<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Settings;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Http\Controllers\CMS\Page\PageController;
use BoomCMS\Support\Facades\Auth;

abstract class Settings extends PageController
{
    /**
     * Directory where views used by this class are stored.
     *
     * @var string
     */
    protected $viewPrefix = 'boomcms::editor.page.settings';

    /**
     * Whether the current user has access to the advanced settings of the permissions group that they're editing.
     *
     * @var bool
     */
    public $allowAdvanced;

    public function admin()
    {
        $this->authorize('edit_page_admin');
    }

    public function children()
    {
        $this->authorize('edit_page_children_basic');
        $this->allowAdvanced = Auth::check('edit_page_children_advanced', $this->page);
    }

    public function delete()
    {
        if (!Auth::canDelete($this->page)) {
            abort(403);
        }
    }

    public function feature()
    {
        $this->authorize('edit_feature_image');
    }

    public function navigation()
    {
        $this->authorize('edit_page_navigation_basic');
        $this->allowAdvanced = Auth::check('edit_page_navigation_advanced', $this->page);
    }

    public function search()
    {
        $this->authorize('edit_page_search_basic');
        $this->allowAdvanced = Auth::check('edit_page_search_advanced', $this->page);
    }

    public function visibility()
    {
        $this->authorize('edit_page');
    }

    public function authorization($role, Page $page = null)
    {
        if (!Auth::check('manage_pages')) {
            parent::authorization($role, $page);
        }
    }
}
