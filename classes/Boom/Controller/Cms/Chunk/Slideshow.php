<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Slideshow extends Boom_Controller_Cms_Chunk
{
	protected $_type = 'slideshow';

	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/slideshow', array(
			'page'	=>	$this->page,
		));
	}

	protected function _preview_chunk()
	{
		$model = ORM::factory('Chunk_Slideshow')->slides($this->request->post('slides'));

		$chunk = new Chunk_Slideshow($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		$this->response->body($chunk->execute());
	}

	protected function _preview_default_chunk()
	{
		$model = new Model_Chunk_Slideshow;

		$chunk = new Chunk_Slideshow($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		$this->response->body($chunk->execute());
	}

	protected function _save_chunk()
	{
		$chunk = parent::_save_chunk();
		$chunk
			->slides($this->request->post('slides'))
			->save_slides();
	}
}