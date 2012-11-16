<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Functions to edit page links.
 *
 * This class extends the Controller_Cms_Page class so that it inherits the $page property and the before() function.
 *
 * @package	Sledge
 * @category	Controllers
 */
class Sledge_Controller_Cms_Page_Link extends Controller_Cms_Page
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
			$link= $this->request->post('link');

			try
			{
				$this->page->add_link($link, FALSE);
			}
			catch (Exception $e)
			{
				// Link is already in use.
				// This response will trigger the JS to ask the user if they want to move the link.
				$this->response->body("link in use");
				return;
			}

			Sledge::log("Added secondary link $link to page " . $this->page->title . "(ID: " . $this->page->id . ")");
		}
		else
		{
			// Display a list of existing secondary links
			$this->template = View::factory("$this->_view_directory/add", array(
				'page'	=> $this->page,
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
			->where('page_id', '=', $this->page->id)
			->where('link', '=', $this->request->post('link'))
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
			'page'	=> $this->page,
		));
	}

	/**
	 * Move a link from one page to another.
	 */
	public function action_move()
	{
		$link = ORM::factory('Page_Link', array(
			'link' => $this->request->query('link')
		));

		if ( ! $link->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if ($this->request->method() == Request::POST)
		{
			// Move the link to this page.
			$link->values(array(
				'page_id'		=>	$this->page->id,
				'is_primary'	=>	FALSE,	// Make sure that it's only a secondary link for the this page.
			))
			->save();
		}
		else
		{
			$this->template = View::factory("$this->_view_directory/move", array(
				'link'		=>	$link,
				'current'	=>	$link->page,
				'page'	=>	$this->page,
			));
		}
	}

	/**
	* Set a link to be the primary link for a page.
	* Checks that the user has the 'edit_primary_link' permission for this page.
	*
	* **Expected POST variables:**
	* Name		|	Type		|	Description
	* ----------------|-----------------|---------------
	* link 		|	string 	|	New primary link for the page.
	*
	*/
	public function action_primary()
	{
		if ( ! $this->auth->logged_in('edit_primary_link', $this->page))
		{
			throw new HTTP_Exception_403;
		}

		$link = $this->request->post('link');

		// Change the page's primary link.
		if ($link != $this->page->primary_link())
		{
			$this->page->add_link($link, TRUE);

			Sledge::log("Added primary link $link to page " . $this->page->title . "(ID: " . $this->page->id . ")");
		}
	}

	/**
	 * Save changes to a link.
	 * Currently the only change which can be made is to toggle whether the link redirects to the primary link.
	 */
	public function action_save()
	{
		$link = ORM::factory('Page_Link', array(
			'link'	=>	$this->request->post('link'),
		));

		$link->redirect = ($this->request->post('redirect') == 'true');
		$link->save();
	}

} // End Sledge_Controller_Cms_Page
