<?php

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
		$chunk = new Chunk_Feature($this->page, $this->_model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$chunk = new Chunk_Feature($this->page, new Model_Chunk_Feature, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}
}