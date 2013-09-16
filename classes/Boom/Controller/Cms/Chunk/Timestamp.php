<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Timestamp extends Boom_Controller_Cms_Chunk
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
		$model = ORM::factory('Chunk_Timestamp')->values($this->request->post());
		$chunk = new Chunk_Timestamp($this->page, $model, $this->request->post('slotname'));

		return $chunk->execute();
	}

	protected function _preview_default_chunk()
	{
		$chunk = new Chunk_Timestamp($this->page, new Model_Chunk_Timestamp, $this->request->post('slotname'));

		return $chunk->execute();
	}
}