<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Controller_Cms_Page_Version_Save extends Controller_Cms_Page_Version
{
	/**
	 *
	 * @var Database
	 */
	public $db;

	/**
	 *
	 * @var	Model_Page_Version
	 */
	public $new_version;


	public function before()
	{
		parent::before();

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

	/**
	 * Saves the content (title, chunks) for a page version.
	 *
	 * This function doesn't have related functions in the other version settings classes.
	 *
	 * All the page settings, slot data, tags etc. are sent via a single json encoded variable in $_POST['data']
	 *
	 */
	public function action_content()
	{
		// Are you allowed to be here?
		$this->page->was_created_by($this->person) OR $this->authorization('edit_page_content', $this->page);

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
					'location'		=>	URL::generate($this->page->parent()->url()->location, urldecode($post->title)),
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
	}

	/**
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Page_Version::embargo()
	 * @uses Model_Page_Version::copy_chunks()
	 */
	public function action_embargo()
	{
		// Call the parent function to check permissions.
		parent::action_embargo();

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

	public function after()
	{
		// Commit the changes.
		$this->db->commit();

		parent::after();
	}
}
