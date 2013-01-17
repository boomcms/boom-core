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
		$this->old_version = $this->page->version();

		if ($this->_method === Request::POST)
		{
			// Create a new version of the page.
			$this->new_version = $this->page->create_version();

			// If the embargo time of the new version is in the past, set the embargo time to NULL
			// This means that if the old version was published, the new version will be a draft.
			// If the embargo time is in the future don't change it.
			if ($this->new_version->embargoed_until <= $_SERVER['REQUEST_TIME'])
			{
				$this->new_version->embargoed_until = NULL;
			}
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
		$this->authorization('editpage_content', $this->page);

		// Start a database transaction.
		Database::instance()->begin();

		// Save page form data is json encoded so get the data and decode it.
		$post = json_decode($this->request->post('data'));

		// Update the title of the new version.
		$this->new_version->title = $post->title;

		// Has the page title been changed?
		// Only generate a new URL from the page title when the title has been changed from 'Untitled'
		// i.e. the page title is being set for the first time.
		if ($this->old_version->title != $post->title AND $this->old_version->title == 'Untitled')
		{
			// Create a new primary link for the page.
			$link = ORM::factory('Page_URL')
				->values(array(
					'location'		=>	URL::generate($this->page->parent()->url(), $post->title),
					'page_id'		=>	$this->page->id,
					'is_primary'	=>	TRUE,
				))
				->create();

			// Put the page's new URL in the response body so that the JS will redirect to the new URL.
			$this->response->body(URL::site($link->location));
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
		$this->authorization('editpage_content', $this->page);

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
				->embargo($embargoed_until)
				->copy_chunks($this->old_version);
		}
	}

	/**
	 * Edit the page's feature image.
	 * Requires the edit_feature_image role.
	 *
	 */
	public function action_feature()
	{
		$this->authorization('edit_feature_image', $this->page);

		if ($this->_method === Request::GET)
		{
			$this->template = View::factory("$this->_view_directory/feature", array(
				'feature_image_id'	=>	$this->old_version->feature_image_id,
			));
		}
		elseif ($this->_method === Request::POST)
		{
			$this->log("Updated the feature image of page " . $this->old_version->title . " (ID: " . $this->page->id . ")");

			$this->new_version
				->set('feature_image_id', $this->request->post('feature_image_id'))
				->create()
				->copy_chunks($this->old_version);
		}
	}

	public function action_template()
	{
		$this->authorization('editpage_template', $this->page);

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