<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ##Controller to edit page options.
 *
 * This class works in much the same way as [Boom_Controller_Cms_Page_Sessions].
 * The different is that page options are versioned, so before changing anything here we create a new version of the page.
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Options extends Controller_Cms_Page_Settings
{
	/**
	 *
	 * @var	Model_Page_Version
	 */
	public $new_version;

	/**
	 *
	 * @var	Model_Page_Version
	 */
	public $old_version;

	/**
	 *
	 * @var	string	Directory where views used by this class are stored.
	 */
	protected $_view_directory = 'boom/editor/page/options';

	public function before()
	{
		parent::before();

		// Store the current version of the page.
		$this->old_version = $this->_page->version();

		if ($this->_method === Request::POST)
		{
			// Create a new version of the page.
			$this->new_version = $this->_page->create_version();
		}
	}

	public function action_embargo()
	{

	}

	/**
	 * Edit the page's feature image.
	 * Requires the edit_feature_image role.
	 *
	 */
	public function action_feature_image()
	{
		$this->_authorization('edit_feature_image', $this->_page);

		if ($this->_method === Request::GET)
		{
			$this->template = View::factory("$this->_view_directory/feature", array(
				'feature_image_id'	=>	$this->old_version->feature_image_id,
			));
		}
		elseif ($this->_method === Request::POST)
		{
			$this->_log("Updated the feature image of page " . $this->old_version->title . " (ID: " . $this->_page->id . ")");

			$this->new_version
				->set('feature_image_id', $this->request->post('feature_image_id'))
				->save();
		}
	}

	public function action_template()
	{

	}
}