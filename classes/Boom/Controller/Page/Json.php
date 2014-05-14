<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page_Json extends Controller_Page
{
	protected $_data = array();

	public function action_show()
	{
		$this->_data = array(
			'id'			=>	$this->page->getId(),
			'title'			=>	$this->page->getTitle(),
			'visible'		=>	$this->page->visible,
			'visible_to'	=>	$this->page->getVisibleTo()->getTimestamp(),
			'visible_from'	=>	$this->page->getVisibleFrom()->getTimestamp(),
			'parent'		=>	$this->page->mptt->parent_id,
			'bodycopy'	=>	Chunk::factory('text', 'bodycopy', $this->page)->text(),
			'standfirst'		=>	Chunk::factory('text', 'standfirst', $this->page)->text(),
		);
	}

	public function after()
	{
		$this->response
			->headers('Content-Type', static::JSON_RESPONSE_MIME)
			->body(json_encode($this->_data));
	}
}