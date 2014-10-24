<?php

class Controller_Cms_Chunk_Timestamp extends Controller_Cms_Chunk
{
	protected $_type = 'timestamp';

	public function action_edit()
	{
		$formats = array();
		foreach (Chunk_Timestamp::$formats as $format)
		{
			$formats[$format] = date($format, $_SERVER['REQUEST_TIME']);
		}

		$this->template = View::factory('boom/editor/slot/timestamp', array(
			'timestamp' => 0,
			'format' => Chunk_Timestamp::$default_format,
			'formats' => $formats,
		));
	}

	protected function _preview_chunk()
	{
		$chunk = new \Boom\Chunk\Timestamp($this->page, $this->_model, $this->request->post('slotname'));

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$chunk = new \Boom\Chunk\Timestamp($this->page, new Model_Chunk_Timestamp, $this->request->post('slotname'));

		return $chunk->execute();
	}
}