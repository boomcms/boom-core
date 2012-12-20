<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Functions to edit page links.
 *
 * This class extends the Controller_Cms_Page class so that it inherits the $page property and the before() function.
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Link extends Controller_Cms_Page
{
	/**
	 *
	 * @var	string	Directory which holds the view files used by the functions in this class.
	 */
	protected $_view_directory = "sledge/editor/links";

	/**
	 * Add a new link to a page.
	 * The link is added as a secondary link.
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * data		|	json		|	json encoded object of containing page ID and link.
	 *
	 */
	public function action_add()
	{
		if ($this->request->post('link'))
		{
			$link= trim($this->request->post('link'));

			// Check that the link isn't already in use.
			$page_link = ORM::factory('Page_Link')
				->where('location', '=', $link)
				->find();

			if ($page_link->loaded() AND $page_link->page_id !== $this->_page->id)
			{
				// Link is being used for a different page.
				// Notify that the link is already in use so that the JS can load a prompt to move the link.
				$this->response->body("link in use");
			}
			elseif ( ! $page_link->loaded())
			{
				//  It's not an old LINK, so create a new one.
				$page_link = ORM::factory('Page_Link')
					->values(array(
						'location'		=>	$link,
						'page_id'		=>	$this->_page->id,
						'is_primary'	=>	FALSE,
					))
					->create();

				$this->_log("Added secondary link $link to page " . $this->_page->version()->title . "(ID: " . $this->_page->id . ")");
			}
		}
		else
		{
			// Display a list of existing secondary links
			$this->template = View::factory("$this->_view_directory/add", array(
				'page'	=> $this->_page,
			));
		}
	}

	/**
	* Remove a link from a page.
	*
	*/
	public function action_delete()
	{
		// Get the link object.
		// Don't delete with direct db query or we won't delete the cached version.
		$link = ORM::factory('Page_Link')
			->where('page_id', '=', $this->_page->id)
			->where('location', '=', $this->request->post('link'))
			->where('is_primary', '=', FALSE)
			->find();

		// Delete the link.
		if ($link->loaded())
		{
			$link->delete();
		}
	}

	/**
	 * List the links associated with a page
	 */
	public function action_list()
	{
		$this->template = View::factory("$this->_view_directory/list", array(
			'page'	=> $this->_page,
		));
	}

	/**
	 * Move a link from one page to another.
	 */
	public function action_move()
	{
		$link = new Model_Page_Link(array(
			'location'	=> $this->request->query('link')
		));

		if ( ! $link->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if ($this->request->method() == Request::POST)
		{
			// Move the link to this page.
			$link->values(array(
				'page_id'		=>	$this->_page->id,
				'is_primary'	=>	FALSE,	// Make sure that it's only a secondary link for the this page.
			))
			->update();
		}
		else
		{
			$this->template = View::factory("$this->_view_directory/move", array(
				'link'		=>	$link,
				'current'	=>	$link->page,
				'page'	=>	$this->_page,
			));
		}
	}

	/**
	 * Save changes to a link.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * link_id 		|	integer 	|	The primary key of the link being edited.
	 * redirect		|	boolean	|	Value for redirect column.
	 * primary		|	boolean	|	Value for the is_primary column.
	 */
	public function action_save()
	{
		// Load the link from the DB by it's primary key.
		$link = new Model_Page_Link($this->request->post('link_id'));

		// Update the link details.
		$link->redirect = $this->request->post('redirect');
		$link->is_primary = $this->request->post('primary');

		// Has this link just become the primary link?
		if ($link->is_primary AND $link->changed('is_primary'))
		{
			$link->make_primary();
		}

		$link->update();
	}
} // End Sledge_Controller_Cms_Page