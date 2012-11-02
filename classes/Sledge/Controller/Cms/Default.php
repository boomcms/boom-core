<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Default CMS controller.
 * This controller doesn't do anything 'useful', it's just for handling requests to http://server.com/cms
 * Going to this URL needs to let someone login, if they're not already, and redirect to the home page if they are logged in.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates Ltd
 */
class Sledge_Controller_Cms_Default extends Sledge_Controller
{
	/**
	 * User has gone to /cms.
	 * If they're not logged in then they'll probably want to do that before anything else, so send them to the login page.
	 * Otherwise send them back to the homepage where they can edit to their heart's content.
	 */
	public function action_index()
	{
		if ( ! $this->auth->logged_in())
		{
			$this->redirect('cms/login');
		}
		else
		{
			$this->redirect('/');
		}	
	}
}
