<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	Sledge
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Chunk_Feature extends Sledge_Controller_Cms_Chunk
{
	public function action_edit()
	{
		$this->template = View::factory('sledge/editor/slot/feature', array(
			'page'	=>	$this->page,
		));
	}

}