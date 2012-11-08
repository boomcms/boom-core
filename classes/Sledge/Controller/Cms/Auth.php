<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * This controller extends Kohana controller because Controller requires a user to be logged in.
 * Which would just be silly here.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Controller_Cms_Auth extends Kohana_Controller
{
	public function action_login()
	{
		if (Auth::instance()->login('admin', 'hello'))
		{
			$this->response
				->body('logged in');
		}
		else
		{
			$this->response
				->body('not logged in');
		}
	}
}
