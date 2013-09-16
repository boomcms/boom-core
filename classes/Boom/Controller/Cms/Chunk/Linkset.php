<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Linkset extends Boom_Controller_Cms_Chunk
{
	protected $_type = 'linkset';

	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/linkset', array(
			'page'	=>	$this->page,
		));
	}

	protected function _preview_chunk()
	{
		$model = ORM::factory('Chunk_Linkset')->links($this->request->post('links'));

		$chunk = new Chunk_Linkset($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$model = new Model_Chunk_Linkset;

		$chunk = new Chunk_Linkset($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _save_chunk()
	{
		$chunk = parent::_save_chunk();
		$chunk
			->links($this->request->post('links'))
			->save_links();
	}
}