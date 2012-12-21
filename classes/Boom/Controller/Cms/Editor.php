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
		// Load the corresponding page.
		$page = new Model_Page($this->request->param('id'));

		// Set some global variables for the view.
		View::bind_global('page', $page);

		// Show the editor topbar
		$this->template = View::factory('boom/editor/toolbar', array(
			'editor'	=>	$this->editor,
		));
	}
}