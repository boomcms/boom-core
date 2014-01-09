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
			'id'			=>	$this->page->id,
			'title'			=>	$this->page->version()->title,
			'visible'		=>	$this->page->visible,
			'visible_to'	=>	$this->page->visible_to,
			'visible_from'	=>	$this->page->visible_from,
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