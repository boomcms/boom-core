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
		$parent = \Boom\Page\Factory::byId($this->request->post('parent'));

		$pages = $this->_get_child_pages($parent);
		$json = $this->_format_pages_as_json($pages);

		$this->response
			->headers('Content-Type', static::JSON_RESPONSE_MIME)
			->body(json_encode($json));
	}

	protected function _get_child_pages($parent)
	{
		$finder = new \Boom\Finder\Page;

		return $finder
			->addFilter(new \Boom\Finder\Page\Filter\ParentPage($parent))
			->find();
	}

	protected function _format_pages_as_json($pages)
	{
		$json_pages = array();

		foreach ($pages as $page)
		{
			$json_pages[] = array(
				'id'			=>	$page->getId(),
				'title'			=>	$page->getTitle(),
				'url'			=>	(string) $page->url(),
				'visible'		=>	(int) $page->isVisible(),
				'has_children'	=>	(int) $page->mptt->has_children(),
			);
		}

		return $json_pages;
	}
}