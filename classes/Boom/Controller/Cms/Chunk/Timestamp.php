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
		$formats = Chunk_Timestamp::$formats;
		foreach ($formats as & $format)
		{
			$format = array($format => date($format, $_SERVER['REQUEST_TIME']));
		}

		$this->template = View::factory('boom/editor/slot/timestamp', array(
			'timestamp' => 0,
			'format' => Chunk_Timestamp::$default_format,
			'formats' => $formats,
		));
	}
}