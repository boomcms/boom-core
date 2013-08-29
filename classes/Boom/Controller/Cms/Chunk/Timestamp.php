<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
class Boom_Controller_Cms_Chunk_Timestamp extends Boom_Controller_Cms_Chunk
{
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

	public function action_preview()
	{
		$model = ORM::factory('Chunk_Timestamp')
			->values(array(
				'format' => $this->request->post('format'),
				'timestamp' => $this->request->post('timestamp'),
			));

		$chunk = new Chunk_Timestamp($this->page, $model, $this->request->post('slotname'));

		$this->response->body($chunk->execute());
	}
}