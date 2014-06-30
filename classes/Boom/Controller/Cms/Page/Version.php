<?php

/**
 * ##Base controller for editing versioned page settings.
 *
 * This class (and it's related classes) work in much the same way as the page settings classes.
 * The key difference is that when one of the settings handled by these classes are changed a new version of the page is created.
 *
 *
 * Editing versioned page settings are handled across three main classes:
 *
 * * [Boom_Controller_Cms_Page_Version]
 *
 *	The base class which is extended by the other classes for permissions checks and other common functionality.
 *
 * * [Boom_Controller_Cms_Page_Version_View]
 *
 *	Displays forms where groups of settings can be changed.
 *
 * * [Boom_Controller_Cms_Page_Versions_Save]
 *
 *	Process submissions from the page settings forms to save changes to the page.
 *  Creates a new version of the page with the new settings.
 *
 * A route set in init.php checks the request method and sends POST requests to [Boom_Controller_Cms_Page_Version_Save] and all other requests to [Boom_Controller_Cms_Page_Version_View]
 *
 *
 * @package	BoomCMS
 * @category	Controllers
 */
abstract class Boom_Controller_Cms_Page_Version extends Controller_Cms_Page
{
	/**
	 *
	 * @var	Model_Page_Version
	 */
	public $old_version;

	/**
	 *
	 * @var	string	Directory where views used by this class are stored.
	 */
	protected $viewDirectory = 'boom/editor/page/version';

	public function before()
	{
		parent::before();

		// Store the current version of the page.
		$this->old_version = $this->page->getCurrentVersion();
	}

	/**
	 * Edit the time when a particular page version becomes live.
	 *
	 * Requires the 'edit_page_content' role.
	 *
	 * @uses Boom_Controller::authorization()
	 */
	public function action_embargo()
	{
		$this->authorization('edit_page_content', $this->page);
	}

	public function action_request_approval()
	{
		$this->authorization('edit_page_content', $this->page);
	}

	/**
	 * Edit the page's templates
	 *
	 * Requires the 'edit_feature_image'edit_page_template' role.
	 *
	 * @uses Boom_Controller::authorization()
	 */
	public function action_template()
	{
		$this->authorization('edit_page_template', $this->page);
	}
}