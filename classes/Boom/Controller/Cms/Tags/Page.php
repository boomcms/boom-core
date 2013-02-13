<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller for viewing an editing page tags.
 *
 * @package BoomCMS
 * @category Controllers
 * @author Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Tags_Page extends Controller_Cms_Tags
{
	public function before()
	{
		parent::before();

		$this->model = new Model_Page($this->request->param('id'));
		$this->ids = array($this->model->id);

		// Before allowing viewing or editing of page tags check for that the current user has the 'edit_page' role for this page.
		$this->authorization('edit_page', $this->model);
	}
} // End Boom_Controller_Cms_Page_Tags