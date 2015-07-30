<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Settings;

use BoomCMS\Core\Page;
use BoomCMS\Http\Controllers\CMS\Page\PageController;

abstract class Settings extends PageController
{
    /**
	 * Directory where views used by this class are stored.
	 *
	 * @var	string
	 */
    protected $viewPrefix = 'boom::editor.page.settings';

    /**
	 * Whether the current user has access to the advanced settings of the permissions group that they're editing.
	 *
	 * @var boolean
	 */
    public $allowAdvanced;

    public function admin()
    {
        $this->authorization('edit_page_admin');
    }

    public function children()
    {
        $this->authorization('edit_page_children_basic');
        $this->allowAdvanced = $this->auth->loggedIn('edit_page_children_advanced', $this->page);
    }

    public function feature()
    {
        $this->authorization('edit_feature_image');
    }

    public function navigation()
    {
        $this->authorization('edit_page_navigation_basic');
        $this->allowAdvanced = $this->auth->loggedIn('edit_page_navigation_advanced', $this->page);
    }

    public function search()
    {
        $this->authorization('edit_page_search_basic');
        $this->allowAdvanced = $this->auth->loggedIn('edit_page_search_advanced', $this->page);
    }

    public function visibility()
    {
        $this->authorization('edit_page');
    }

    public function authorization($role, Page\Page $page = null)
    {
        if ( ! $this->auth->loggedIn('manage_pages')) {
            parent::authorization($role, $this->page);
        }
    }
}
