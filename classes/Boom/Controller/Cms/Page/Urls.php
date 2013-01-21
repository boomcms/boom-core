<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Functions to edit page urls.
 *
 * This class extends the Controller_Cms_Page class so that it inherits the $page property and the before() function.
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Urls extends Controller_Cms_Page
{
	/**
	 *
	 * @var	string	Directory which holds the view files used by the functions in this class.
	 */
	protected $_view_directory = "boom/editor/urls";

	/**
	 *
	 * @var Model_Page_URL
	 */
	public $page_url;

	public function before()
	{
		parent::before();

		// Create a page_url model.
		$this->page_url = new Model_Page_URL;
	}

	/**
	 * Add a new url to a page.
	 * The url is added as a secondary url.
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * data		|	json		|	json encoded object of containing page ID and url.
	 *
	 */
	public function action_add()
	{
		if ($this->request->post('url'))
		{
			$url= trim($this->request->post('url'));

			// Check that the url isn't already in use.
			$page_url = ORM::factory('Page_URL')
				->where('location', '=', $url)
				->find();

			if ($page_url->loaded() AND $page_url->page_id !== $this->page->id)
			{
				// Url is being used for a different page.
				// Notify that the url is already in use so that the JS can load a prompt to move the url.
				$this->response->body("url in use");
			}
			elseif ( ! $page_url->loaded())
			{
				//  It's not an old URL, so create a new one.
				$page_url = ORM::factory('Page_URL')
					->values(array(
						'location'		=>	$url,
						'page_id'		=>	$this->page->id,
						'is_primary'	=>	FALSE,
					))
					->create();

				$this->log("Added secondary url $url to page " . $this->page->version()->title . "(ID: " . $this->page->id . ")");
			}
		}
		else
		{
			// Display a list of existing secondary urls
			$this->template = View::factory("$this->_view_directory/add", array(
				'page'	=> $this->page,
			));
		}
	}

	/**
	* Remove a url from a page.
	*
	*/
	public function action_delete()
	{
		// Get the page URL object
		$this->page_url
			->where('page_id', '=', $this->page->id)
			->where('location', '=', $this->request->post('location'))
			->where('is_primary', '=', FALSE)					// Only allow deleting the page URL if it isn't the primary URL
			->find();

		// Delete the url.
		if ($this->page_url->loaded())
		{
			$this->page_url->delete();
		}
	}

	/**
	 * List the urls associated with a page
	 */
	public function action_list()
	{
		$this->template = View::factory("$this->_view_directory/list", array(
			'page'	=> $this->page,
		));
	}

	/**
	 * Move a url from one page to another.
	 */
	public function action_move()
	{
		$url = new Model_Page_URL(array(
			'location'	=> $this->request->query('url')
		));

		if ( ! $url->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if ($this->request->method() == Request::POST)
		{
			// Move the url to this page.
			$url->values(array(
				'page_id'		=>	$this->page->id,
				'is_primary'	=>	FALSE,	// Make sure that it's only a secondary url for the this page.
			))
			->update();
		}
		else
		{
			$this->template = View::factory("$this->_view_directory/move", array(
				'url'		=>	$url,
				'current'	=>	$url->page,
				'page'	=>	$this->page,
			));
		}
	}

	/**
	 * Save changes to a url.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * url_id 		|	integer 	|	The primary key of the url being edited.
	 * redirect		|	boolean	|	Value for redirect column.
	 * primary		|	boolean	|	Value for the is_primary column.
	 */
	public function action_save()
	{
		// Load the url from the DB by it's primary key.
		$url = new Model_Page_URL($this->request->post('url_id'));

		// Update the url details.
		$url->redirect = $this->request->post('redirect');
		$url->is_primary = $this->request->post('primary');

		// Has this url just become the primary url?
		if ($url->is_primary AND $url->changed('is_primary'))
		{
			$url->make_primary();

			// make_primary() calls update() so no need to go any further.
			return;
		}

		// Save the changes.
		$url->update();
	}
} // End Boom_Controller_Cms_Page