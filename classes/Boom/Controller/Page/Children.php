<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
*/
class Boom_Controller_Page_Children extends Boom_Controller
{
	public function action_json()
	{
		$parent_id = $this->request->post('parent');

		$pages = $this->_get_child_pages($parent_id);
		$json = $this->_format_pages_as_json($pages);

		$this->response
			->headers('Content-Type', 'application/json')
			->body(json_encode($json));
	}

	protected function _get_child_pages($parent_id)
	{
		return Finder::pages()
			->which_are_children_of_the_page_by_id($parent_id)
			->apply_default_sort()
			->get_results();
	}

	protected function _format_pages_as_json($pages)
	{
		$json_pages = array();

		foreach ($pages as $page)
		{
			$json_pages[] = array(
				'id'			=>	$page->id,
				'title'			=>	$page->version()->title,
				'url'			=>	(string) $page->url(),
				'visible'		=>	(int) $page->is_visible(),
				'has_children'	=>	(int) $page->mptt->has_children(),
			);
		}

		return $json_pages;
	}
}