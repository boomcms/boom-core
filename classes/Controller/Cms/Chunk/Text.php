<?php

class Controller_Cms_Chunk_Text extends Controller_Cms_Chunk
{
	protected $_type = 'text';

	protected function _preview_chunk() {}

	protected function _preview_default_chunk() {}

	protected function _save_chunk()
	{
		return $this->_model = ORM::factory("Chunk_".ucfirst($this->_type))
			->values($this->request->post())
			->set('page_vid', $this->_new_version->id)
			->create();
	}
}