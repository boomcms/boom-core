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

		$this->ids = array_unique(explode('-', $this->request->param('id')));

		if (count($this->ids) === 1)
		{
			$this->model = new Model_Asset($this->request->param('id'));
		}
		else
		{
			$this->model = new Model_Asset;
		}

		$this->type = Model_Tag::ASSET;

		$this->authorization('manage_assets');
	}
} // End Boom_Controller_Cms_Asset_Tags