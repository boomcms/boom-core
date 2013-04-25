<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page_Rss extends Controller_Page
{
	public function action_show()
	{
		// RSS feeds for a page display a list of the child pages so get the children of the current page.
		$pages = ORM::factory('Page')
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->with_current_version($this->editor)
			->where('page_mptt.parent_id', '=', $this->page->id)
			->order_by('visible_from', 'desc')
			->find_all();

		$this->response
			->headers('Content-Type', 'application/rss+xml')
			->body(View::factory('feeds/rss', array(
				'page'	=>	$this->page,
				'pages'	=>	$pages,
			)));
	}
}