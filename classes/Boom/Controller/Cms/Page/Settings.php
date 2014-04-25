<?php defined('SYSPATH') OR die('No direct script access.');

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
abstract class Boom_Controller_Cms_Page_Settings extends Controller_Cms_Page
{
	/**
	 * Directory where views used by this class are stored.
	 *
	 * @var	string
	 */
	protected $_view_directory = 'boom/editor/page/settings';

	/**
	 * Whether the current user has access to the advanced settings of the permissions group that they're editing.
	 *
	 * @var boolean
	 */
	public $allow_advanced;

	/**
	 * **Edit the page admin settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Internal name
	 *
	 */
	public function action_admin()
	{
		// Permissions check
		$this->authorization('edit_page_admin', $this->page);
	}

	/**
	 * **Edit the child page settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *    * Default child template
	 *    * Child ordering policy
	 *  * Advanced:
	 *    * Children visible in nav
	 *    * Children visible in CMS nav
	 *    * Default child URL prefix
	 *    * Default grandchild template
	 *
	 */
	public function action_children()
	{
		// Permissions check
		// These settings are divided into basic and advanced.
		// We only need to check for the basic permissions here
		// If they can't edit the basic stuff then they shouldn't have the advanced settings either.
		$this->authorization('edit_page_children_basic', $this->page);

		// Is the current user allowed to edit the advanced settings?
		$this->allow_advanced = $this->auth->logged_in('edit_page_children_advanced', $this->page);
	}

	/**
	 * **Edit page navigation settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *    * Visible in navigation
	 *    * Visible in CMS navigation
	 *  * Advanced:
	 *    * Parent page
	 *
	 */
	public function action_navigation()
	{
		// Permissions check
		// The need to have a minimum of being able to edit the basic navigation settings.
		// If they can't edit the basic settings they won't be able to edit the advanced settings either.
		$this->authorization('edit_page_navigation_basic', $this->page);

		// Is the current user allowed to edit the advanced settings?
		$this->allow_advanced = $this->auth->logged_in('edit_page_navigation_advanced', $this->page);
	}

	/**
	 * ** Edit page search settings. **
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *     * Keywords
	 *     * Description
	 *  Advanced:
	 *     * External indexing
	 *     * Internal indexing
	 *
	 */
	public function action_search()
	{
		// Check permissions
		$this->authorization('edit_page_search_basic', $this->page);

		// Is the current user allowed to edit the advanced settings?
		$this->allow_advanced = $this->auth->logged_in('edit_page_search_advanced', $this->page);
	}

	public function action_template()
	{
		$this->authorization('edit_page_template', $this->page);
	}

	/**
	 * ** Edit page visibility settings. **
	 *
	 * Settings in this group:
	 *  * visible
	 *  * visible from
	 *  * visible to
	 *
	 */
	public function action_visibility()
	{
		// Permissions check.
		$this->authorization('edit_page', $this->page);
	}

	public function authorization($role, \Model_Page $page)
	{
		if ( ! $this->auth->logged_in('manage_pages'))
		{
			parent::authorization($role, $page);
		}
	}
}