<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Settings;

use BoomCMS\Core\Page;
use BoomCMS\Core\Controllers\CMS\Page\PageController;

/**
 * ##Base controller for editing page settings.
 *
 * Editing page settings are handled across three main classes:
 *
 * * [Boom_Controller_Cms_Page_Settings]
 *
 *	The base class which is extended by the other classes for permissions checks and other common functionality.
 *
 * * [Boom_Controller_Cms_Page_Settings_View]
 *
 *	Displays forms where groups of settings can be changed.
 *
 * * [Boom_Controller_Cms_Page_Settings_Save]
 *
 *	Process submissions from the page settings forms to save changes to the page.
 *
 * A route set in init.php checks the request method and sends POST requests to [Boom_Controller_Cms_Page_Settings_Save] and all other requests to [Boom_Controller_Cms_Page_Settings_View]
 *
 *
 * @package	BoomCMS
 * @category	Controllers
 */
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

    public function admin(Page\Page $page)
    {
        $this->authorization('edit_page_admin', $page);
    }

    public function children(Page\Page $page)
    {
        $this->authorization('edit_page_children_basic', $page);
        $this->allowAdvanced = $this->auth->loggedIn('edit_page_children_advanced', $page);
    }

    public function feature(Page\Page $page)
    {
        $this->authorization('edit_feature_image', $page);
    }

    public function navigation(Page\Page $page)
    {
        $this->authorization('edit_page_navigation_basic', $page);
        $this->allowAdvanced = $this->auth->loggedIn('edit_page_navigation_advanced', $page);
    }

    public function search(Page\Page $page)
    {
        $this->authorization('edit_page_search_basic', $page);
        $this->allowAdvanced = $this->auth->loggedIn('edit_page_search_advanced', $page);
    }

    public function visibility(Page\Page $page)
    {
        $this->authorization('edit_page', $page);
    }

    public function authorization($role, Page\Page $page = null)
    {
        if ( ! $this->auth->loggedIn('manage_pages')) {
            parent::authorization($role, $page);
        }
    }
}
