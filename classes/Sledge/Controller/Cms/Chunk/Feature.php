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

	public function action_preview()
	{
		$model = ORM::factory('Chunk_Feature')
			->values(array(
				'target_page_id'	=>	$this->request->query('preview_target_rid')
			));

		$chunk = new Chunk_Feature($this->page, $model, $this->request->query('slotname'), TRUE);
		$chunk->template($this->request->query('template'));

		$this->response->body($chunk->execute());
	}
}