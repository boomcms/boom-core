<?php

class Boom_Controller_Cms_Chunk_Slideshow extends Boom_Controller_Cms_Chunk
{
	protected $_type = 'slideshow';

	public function action_edit()
	{
		$chunk = Chunk::find('slideshow', $this->request->query('slotname'), $this->page->getCurrentVersion());

		$this->template = View::factory('boom/editor/slot/slideshow', array(
			'slides' => $chunk->slides(),
		));
	}

	protected function _preview_chunk()
	{
		$chunk = new Chunk_Slideshow($this->page, $this->_model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$model = new Model_Chunk_Slideshow;

		$chunk = new Chunk_Slideshow($this->page, $model, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _save_chunk()
	{
		$chunk = parent::_save_chunk();
		$chunk
			->slides($this->request->post('slides'))
			->save_slides();
	}
}