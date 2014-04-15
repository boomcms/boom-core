<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Text extends Boom_Controller_Cms_Chunk
{
	protected $_type = 'text';

	protected function _preview_chunk()
	{
		$chunk = new Chunk_Text($this->page, $this->_model, $this->request->post('slotname'));

		if ($template = $this->request->post('template'))
		{
			$chunk->template($template);
		}

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$chunk = new Chunk_Text($this->page, new Model_Chunk_Text, $this->request->post('slotname'));
		$chunk->template($this->request->post('template'));

		return $chunk->execute();
	}

	protected function _save_chunk()
	{
		return $this->_model = ORM::factory("Chunk_".ucfirst($this->_type))
			->values($this->request->post())
			->clean_text()
			->set('page_vid', $this->_new_version->id)
			->create();
	}
}