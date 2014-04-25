<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller for the page manager.
 * Allows viewing a list of the pages in the CMS and, in the future, doing stuff with that list.
 *
 * Not to be confused with [Controller_Cms_Page] which is used for editing a single page.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Tayor
 *
 */
class Boom_Controller_Cms_Pages extends Boom_Controller
{
	public function before()
	{
		parent::before();

		$this->authorization('manage_pages');
	}

	public function action_index()
	{
		$pages = ORM::factory('Page')
			->where('deleted', '=', false)
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('lvl', '=', 1)
			->find_all();

		$this->template = View::factory('boom/pages/index', array(
			'pages'	=>	$pages,
		));
	}
}