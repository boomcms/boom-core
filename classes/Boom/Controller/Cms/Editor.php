<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Editor extends Boom_Controller
{
	/**
	 * Sets the page editor state.
	 * Toggles the editor between editing, preview published versions, or previewing all versions.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * state		|	string 	|	Either disabled, preview-all, or edit.
	 *
	 * @uses	$this->editor->state()
	 */
	public function action_state()
	{
		// Get the state from the POST data.
		$state = $this->request->post('state');

		// Convert the text from POST to an integer.
		$numeric_state = constant("Editor::" . strtoupper($state));

		// Check that the state is valid
		if ($numeric_state === NULL)
		{
			throw new Kohana_Exception("Invalid editor state: :state", array(
				':state'	=>	$state,
			));
		}

		// Save the state to the session data.
		$this->editor->state($numeric_state);
	}

	/**
	 * Displays the CMS interface with buttons for add page, settings, etc.
	 * Called from an iframe when logged into the CMS.
	 * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>)
	 */
	public function action_toolbar()
	{
		// Get the page ID from the URL paramaters.
		// Get this from the request params now and we don't have to call Model_Page::__get to use the page ID later.
		$page_id = $this->request->param('id');

		// Load the corresponding page.
		$page = new Model_Page($page_id);

		// Log the current user as editing this page.
		// Try and find existing details from db / cache.
		$person_page = new Model_Person_Page(array(
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
		$this->template = View::factory('boom/editor/toolbar', array(
			'editor'	=>	$this->editor,
		));
	}
}