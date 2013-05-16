<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk_Asset extends Boom_Controller_Cms_Chunk
{
	public function action_preview()
	{
		$data = $this->request->post();
		
		$model = ORM::factory('Chunk_Asset')
			->values(array(
				'asset_id'	=>	$data['data']['asset_id'],
				'link'		=>	$data['data']['link'],
				'caption'	=>	$data['data']['caption'],
				'description'	=>	$data['data']['caption'],
			));

		$chunk = new Chunk_Asset($this->page, $model, $data['slotname']);
		$chunk->template($data['template']);

		$this->response->body($chunk->execute());
	}
}
