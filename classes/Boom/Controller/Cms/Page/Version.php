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
class Boom_Controller_Cms_Page_Version extends Controller_Cms_Page_Settings
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
	protected $_view_directory = 'boom/editor/page/version';

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

	/**
	 * Saves
	 */
	public function action_content()
	{

	}

	public function action_embargo()
	{
		$this->_authorization('edit_page_content', $this->_page);

		if ($this->_method === Request::GET)
		{

		}
		elseif ($this->_method === Request::POST)
		{

		}
	}

	/**
	 * Edit the page's feature image.
	 * Requires the edit_feature_image role.
	 *
	 */
	public function action_feature()
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
				->save()
				->copy_chunks($this->old_version);
		}
	}

	public function action_template()
	{
		$this->_authorization('edit_page_template', $this->_page);

		if ($this->_method === Request::GET)
		{
			$this->template = View::factory("$this->_view_directory/template", array(
				'template_id'	=>	$this->old_version->template_id,
				'templates'	=>	 ORM::factory('Template')
					->names()
			));
		}
		elseif ($this->_method === Request::POST)
		{
			$this->new_version
				->set('template_id', $this->request->post('template_id'))
				->save()
				->copy_chunks($this->old_version);
		}
	}
}