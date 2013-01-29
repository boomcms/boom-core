<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk_Feature extends Boom_Controller_Cms_Chunk
{
	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/feature', array(
			'page'	=>	$this->page,
		));
	}

	public function action_preview()
	{
		$data = $this->request->post();
		
		$model = ORM::factory('Chunk_Feature')
			->values(array(
				'target_page_id'	=>	$data['data']['target_page_id']
			));

		$chunk = new Chunk_Feature($this->page, $model, $data['slotname']);
		$chunk->template($data['template']);

		$this->response->body($chunk->execute());
	}
}
