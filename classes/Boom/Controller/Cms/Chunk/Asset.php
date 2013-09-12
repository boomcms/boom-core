<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Asset extends Boom_Controller_Cms_Chunk
{
	protected $_type = 'asset';

	protected function _preview_chunk()
	{
		$model = ORM::factory('Chunk_Asset')->values($this->request->post());

		$chunk = new Chunk_Asset($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		$this->response->body($chunk->execute());
	}

	protected function _preview_default_chunk()
	{
		$chunk = new Chunk_Asset($this->page, new Model_Chunk_Asset, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		$this->response->body($chunk->execute());
	}
}