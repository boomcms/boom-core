<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Settings;

use BoomCMS\Http\Controllers\CMS\Page\PageController;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('editAdmin', $this->page);
    }

    public function children()
    {
        $this->authorize('editChildrenBasic', $this->page);
        $this->allowAdvanced = Auth::check('editChildrenAdvanced', $this->page);
    }

    public function delete()
    {
        $this->authorize('delete', $this->page);
    }

    public function feature()
    {
        $this->authorize('editFeature', $this->page);
    }

    public function navigation()
    {
        $this->authorize('editNavBasic', $this->page);
        $this->allowAdvanced = Auth::check('editNavAdvanced', $this->page);
    }

    public function search()
    {
        $this->authorize('editSearchBasic', $this->page);
        $this->allowAdvanced = Auth::check('editSearchAdvanced', $this->page);
    }

    public function visibility()
    {
        $this->authorize('edit', $this->page);
    }
}
