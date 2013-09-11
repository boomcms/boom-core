<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Feature extends Boom_Controller_Cms_Chunk
{
	protected $_type = 'feature';

	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/feature', array(
			'page'	=>	$this->page,
		));
	}

	protected function _preview_chunk()
	{
		$model = ORM::factory('Chunk_Feature')->values($this->request->post('target_page_id'));

		$chunk = new Chunk_Feature($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		$this->response->body($chunk->execute());
	}

	protected function _preview_default_chunk()
	{
		$chunk = new Chunk_Feature($this->page, new Model_Chunk_Feature, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		$this->response->body($chunk->execute());
	}
}