<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk_Slideshow extends Boom_Controller_Cms_Chunk
{
	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/slideshow', array(
			'page'	=>	$this->page,
		));
	}

	public function action_save()
	{
		$slides = $this->request->post('slides');
	}
}