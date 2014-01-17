<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller for viewing and editing asset tags.
 *
 * @package BoomCMS
 * @category Controllers
 * @author Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Tags_Asset extends Controller_Cms_Tags
{
	public function before()
	{
		parent::before();

		if ($this->request->param('id') != 0)
		{
			$this->ids = array_unique(explode('-', $this->request->param('id')));
		}

		$asset_id = (count($this->ids) === 1)? $this->request->param('id') : NULL;
		$this->model = new Model_Asset($asset_id);

		$this->authorization('manage_assets');
	}

	public function action_list()
	{
		parent::action_list();

		$message = (count($this->tags))? 'asset.hastags' : 'asset.notags';
		$this->template->set('message', Kohana::message('boom', $message));
	}
}