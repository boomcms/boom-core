<?php

class Controller_Cms_Chunk_Tag extends Controller_Cms_Chunk
{
	protected $_type = 'tag';

	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/tag', array(
			'current_tag' => new Model_Tag($this->request->query('tag_id')),
		));
	}

	protected function _preview_chunk()
	{
		$chunk = new Chunk_Tag($this->page, $this->_model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$chunk = new Chunk_Tag($this->page, new Model_Chunk_Tag, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}
}