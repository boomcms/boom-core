<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Editor extends Sledge_Controller
{
	/**
	 * Returns a json encoded array of the people currently viewing the same page as this user in the editor.
	 * This is queried periodically with an AJAX request to tell the user when other people are viewing the same page.
	 *
	 * The controller performs two functions:
	 *	*	Updates the last_active time of the current user for the given page.
	 *	*	Returns a json_encoded array of the other users who are viewing the given page and when they were last active.
	 *
	 * When making the request the javascript sends the ID of the page that is open in the browser window.
	 * This controller SHOULD return an array of people viewing that page.
	 * The controller SHOULD check that the current user is recorded as viewing the page specificed. This prevents people typing things into their browser (or making scripts) to get information for which they have no need.
	 * When the user is not recorded as viewing the specified page the controller SHOULD return a json encoded empty array.
	 *
	 * @return 	string	A json encoded array
	 */
	public function action_active_people()
	{
		// Get the page ID from the request paramaters.
		$page_id = $this->request->param('id');

		// Load the record relating to this user and this page from the database.
		$us = ORM::factory('Person_Page', array(
			'person_id'	=>	$this->person->id,
			'page_id'		=>	$page_id,
		));

		// Are they known to be viewing this page?
		if ($us->loaded())
		{
			// Update the time that we were last active on this page.
			// We use to ensure that people who closed their browser and wandered off don't appear as editing the page.
			$us->last_active = $_SERVER['REQUEST_TIME'];
			$us->save();

			// Get the details of the other people viewing this page who were last active in the past 2 minutes
			// The JS polls the server every 30 seconds at the moment so two moments allows for a couple of failed requests.
			// We're interesed in their person ID, when they started looking at the page, and whether they've saved the page.
			$details = DB::select('people_pages.person_id', 'people_pages.since', 'people_pages.saved', 'people.name')
				->from('people_pages')
				->join('people', 'inner')
				->on('people_pages.person_id', '=', 'people.id')
				->where('people_pages.page_id', '=', $us->page_id)
				->where('people_pages.person_id', '!=', $this->person->id)
				->where('people_pages.last_active', '>=', $_SERVER['REQUEST_TIME'] - 120)
				->execute();

			// Prepare a response array.
			$response = array();

			// Populate the response array with the details of each person.
			foreach ($details as $detail)
			{
				$response[] = array(
					'id'			=>	$detail['person_id'],
					'name'		=>	$detail['name'],
					'since'		=>	$detail['since'],
					'saved'		=>	(bool) $detail['saved'],
				);
			}
		}
		else
		{
			// Send an empty array as the response.
			$response = array();
		}

		// Populate the result object.
		$this->response
			->headers('content-type', 'application/json')
			->body(json_encode($response));
	}

	/**
	 * Sets the page editor state.
	 * Toggles the editor between editing, preview published versions, or previewing all versions.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * state		|	string 	|	Either preview-published, preview-all, or edit.
	 *
	 * @uses	Editor::state()
	 */
	public function action_state()
	{
		$state = $this->request->post('state');

		// Save the state to the session data.
		Editor::state($state);
	}

	/**
	 * Displays the CMS interface with buttons for add page, settings, etc.
	 * Called from an iframe when logged into the CMS.
	 * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>)
	 */
	public function action_toolbar()
	{
		// Get the page ID from the URL paramaters.
		$page_id = $this->request->param('id');

		// Load the corresponding page.
		$page = ORM::factory('Page', $page_id);

		// Log the current user as editing this page.
		// Try and find existing details from db / cache.
		$person_page = ORM::factory('Person_Page', array(
			'person_id'	=>	$this->person->id,
			'page_id'		=>	$page_id
		));

		// Set the values and save.
		// By also setting the person ID we ensure that if $person_page wasn't loaded it will be created.
		// If it was loaded it will be updated.
		$person_page->values(array(
			'person_id'		=>	$this->person->id,
			'page_id'			=>	$page_id,
			'since'			=>	$_SERVER['REQUEST_TIME'],
			'last_active'		=>	$_SERVER['REQUEST_TIME'],
			'saved'			=>	FALSE,
		));

		// Save the record.
		$person_page->save();

		// Set some global variables for the view.
		View::bind_global('page', $page);

		// Show the editor topbar
		$this->template = View::factory('sledge/editor/toolbar');
	}
}