<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Controller_Cms_Page_Version_Save extends Controller_Cms_Page_Version
{
	/**
	 * @var Database
	 */
	public $db;

	/**
	 * @var	Model_Page_Version
	 */
	public $new_version;

	public function before()
	{
		parent::before();

		$this->_csrf_check();

		// Start a database transaction.
		$this->db = Database::instance();
		$this->db->begin();

		// Create a new version of the page.
		$this->new_version = $this->page->create_version($this->old_version, array(
			'edited_by'	=>	$this->person->id,
		));

		// If the embargo time of the new version is in the past, set the embargo time to NULL
		// This means that if the old version was published, the new version will be a draft.
		// If the embargo time is in the future don't change it.
		if ($this->new_version->embargoed_until <= $_SERVER['REQUEST_TIME'])
		{
			$this->new_version->embargoed_until = NULL;
		}
	}

	public function action_embargo()
	{
		parent::action_embargo();

		$embargoed_until = $this->request->post('embargoed_until')? strtotime($this->request->post('embargoed_until')) : $_SERVER['REQUEST_TIME'];

		$this->new_version
			->create()
			->embargo($embargoed_until)
			->copy_chunks($this->old_version);

		$this->new_version->is_published() AND $this->page->remove_drafts();
	}

	/**
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Page_Version::copy_chunks()
	 */
	public function action_feature()
	{
		// Call the parent function to check permissions.
		parent::action_feature();

		$this->log("Updated the feature image of page " . $this->old_version->title . " (ID: " . $this->old_version->page_id . ")");

		$this->new_version
			->set('feature_image_id', $this->request->post('feature_image_id'))
			->create()
			->copy_chunks($this->old_version);
	}

	/**
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Page_Version::copy_chunks()
	 */
	public function action_template()
	{
		// Call the parent function to check permissions.
		parent::action_template();

		$this->new_version
			->set('template_id', $this->request->post('template_id'))
			->create()
			->copy_chunks($this->old_version);
	}

	public function action_title()
	{
		$this->new_version->set('title', $this->request->post('title'));

		if ($this->new_version->changed('title') AND $this->old_version->title == 'Untitled' AND ! $this->page->mptt->is_root())
		{
			// Create a new primary link for the page.
			$link = ORM::factory('Page_URL')
				->values(array(
					'location'	=>	URL::generate($this->page->parent()->url()->location, $this->request->post('title')),
					'page_id'	=>	$this->page->id,
					'is_primary'	=>	TRUE,
				))
			->create();

			// Put the page's new URL in the response body so that the JS will redirect to the new URL.
			$this->response->body(json_encode(array(
				'location' => URL::site($link->location, $this->request),
			)));
		}

		$this->new_version
			->create()
			->copy_chunks($this->old_version);
	}

	public function after()
	{
		// Commit the changes.
		$this->db->commit();

		if ( ! $this->response->body())
		{
			$this->response->body($this->new_version->status());
		}

		parent::after();
	}
}