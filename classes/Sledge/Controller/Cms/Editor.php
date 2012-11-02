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
	 * Sets the page editor state.
	 *
	 * **Expected POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * state		|	string 	|	Either preview-published, preview-all, or edit.
	 *
	 */
	public function action_state()
	{
		$state = $this->request->post('state');

		// Save the state to the session data.
		Editor::state($state);
	}
}
