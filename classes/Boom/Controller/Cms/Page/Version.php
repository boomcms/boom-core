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
	 * Saves the content (title, chunks) for a page version.
	 *
	 *
	 * **Expected POST variables:**
	 * Name		|	Type	|	Description
	 * ---------------|-----------|---------------
	 * data		|	json	|	All the page settings, slot data, tags etc. are sent via a single json encoded variable...
	 *
	 */
	public function action_content()
	{
		// Are you allowed to be here?
		$this->_authorization('edit_page_content', $this->_page);

		// Start a database transaction.
		Database::instance()->begin();

		// Save page form data is json encoded so get the data and decode it.
		$post = json_decode($this->request->post('data'));

		// Has the page title been changed?
		if ($this->old_version->title != $post->title)
		{
			// Update the title of the new version.
			$this->new_version->title = $post->title;

			// TODO: update the primary URL of the page the first time a title is saved.
		}

		// Save the new version.
		$this->new_version->create();

		// Update chunks.

		// Used to build an array of slotnames submitted.
		$slotnames = array();

		foreach ( (array) $post->slots as $type => $obj)
		{
			foreach (get_object_vars($obj) as $name => $chunk_data)
			{
				$name = trim($name);

				// Add this slot to the array of slotnames.
				// Do this even if the chunk is being deleted so that deleted chunks won't be copied from the old version.
				$slotnames[] = $name;

				if ( ! isset($obj->delete))
				{
					$chunk = $this->new_version
						->add_chunk($type, $name, (array) $chunk_data);

					if ($type == 'slideshow')
					{
						// Add slides to the slideshow chunk.
						$chunk
							->slides($chunk_data->slides)
							->save_slides();
					}
					elseif ($type == 'linkset')
					{
						// Add links to the linkset chunk.
						$chunk
							->links($chunk_data->links)
							->save_links();
					}
				}
			}
		}

		// Import any chunks which weren't saved from the old version to the new version.
		$this->new_version->copy_chunks($this->old_version, $slotnames);

		// Commit the changes.
		Database::instance()->commit();
	}

	public function action_embargo()
	{
		$this->_authorization('edit_page_content', $this->_page);

		if ($this->_method === Request::GET)
		{
			$this->template = View::factory("$this->_view_directory/embargo", array(
				'version'	=>	$this->old_version,
			));
		}
		elseif ($this->_method === Request::POST)
		{
			$embargoed_until = $this->request->post('embargoed_until');

			// If an embargo time hasn't been given, or the embargo has been removed
			// Use the time of the request
			// This means that the embargo time is in the past - the version is published.
			if ( ! ($embargoed_until AND $this->request->post('embargoed')))
			{
				$embargoed_until = $_SERVER['REQUEST_TIME'];
			}
			else
			{
				$embargoed_until = strtotime($embargoed_until);
			}

			// Updated the embargo time of the new version.
			$this->new_version
				->create()
				->embargo($embargoed_until);
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
				->create()
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
				->create()
				->copy_chunks($this->old_version);
		}
	}
}