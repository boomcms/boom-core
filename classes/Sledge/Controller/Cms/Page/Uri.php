<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Functions to edit page URIs.
 *
 * This class extends the Controller_Cms_Page class so that it inherits the $_page property and the before() function.
 *
 * @package	Sledge
 * @category	Controllers
 */
class Sledge_Controller_Cms_Page_Uri extends Controller_Cms_Page
{
	/**
	 * Add a new URI to a page.
	 * The URI is added as a secondary URI.
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type	|	Description
	 * ----------|-----------|---------------
	 * data		|	json	|	json encoded object of containing page ID and URI.
	 *
	 */
	public function action_add()
	{
		if ($this->request->post('uri'))
		{
			$uri = $this->request->post('uri');

			try
			{
				$this->page->add_uri($uri, FALSE);
			}
			catch (Exception $e)
			{
				// URI is already in use.
				// This response will trigger the JS to ask the user if they want to move the URI.
				$this->response->body("uri in use");
				return;
			}

			Sledge::log("Added secondary URI $uri to page " . $this->page->title . "(ID: " . $this->page->id . ")");
		}
		else
		{
			// Display a list of existing secondary URIs
			$this->template = View::factory('sledge/editor/uri/add');
			$this->template->page = $this->page;
		}
	}

	/**
	* Remove a URI from a page.
	*
	*/
	public function action_delete()
	{
		// Get the URI object.
		// Don't delete with direct db query or we won't delete the cached version.
		$uri = ORM::factory('Page_URI')
			->where('page_id', '=', $this->page->id)
			->where('uri', '=', $this->request->post('uri'))
			->where('is_primary', '=', FALSE)
			->find();

		// Delete the URI.
		if ($uri->loaded())
		{
			$uri->delete();
		}
	}

	/**
	 * List the URIs associated with a page
	 */
	public function action_list()
	{
		$this->template = View::factory('sledge/editor/page/settings/uris');
		$this->template->page = $this->page;
	}

	/**
	 * Move a URI from one page to another.
	 */
	public function action_move()
	{
		$uri = $this->request->query('uri');
		$uri = ORM::factory('Page_uri', array('uri' => $uri));

		if ($uri->loaded())
		{
			if ($this->request->method() == Request::POST)
			{
				// Move the URI to this page.
				$uri->page = $this->page;

				// Make sure that it's only a secondary URI for the this page.
				$uri->is_primary = FALSE;

				$uri->save();
			}
			else
			{
				$this->template = View::factory('sledge/editor/uri/move', array(
					'uri'		=>	$uri,
					'current'	=>	$uri->page,
					'page'		=>	$this->page,
				));
			}
		}
	}

	/**
	* Set a URI to be the primary URI for a page.
	* Checks that the user has the 'edit_primary_uri' permission for this page.
	*
	* **Expected POST variables:**
	* Name		|	Type	|	Description
	* ----------|-----------|---------------
	* uri 		|	string 	|	New primary URI for the page.
	*
	*/
	public function action_primary()
	{
		$page = $this->page;

		if ( ! $this->auth->logged_in('edit_primary_uri', $page))
		{
			throw new HTTP_Exception_403;
		}

		$uri = $this->request->post('uri');

		// Change the page's primary URI.
		if ($uri != $page->get_primary_uri())
		{
			$page->add_uri($uri, TRUE);

			Sledge::log("Added primary URI $uri to page " . $this->page->title . "(ID: " . $this->page->id . ")");
		}
	}

	/**
	 * Save changes to a URI.
	 * Currently the only change which can be made is to toggle whether the URI redirects to the primary URI.
	 */
	public function action_save()
	{
		$uri = $this->request->post('uri');
		$uri = ORM::factory('Page_URI', array('uri' => $uri));

		$uri->redirect = $this->request->post('redirect') == 'true';
		$uri->save();
	}

} // End Sledge_Controller_Cms_Page
