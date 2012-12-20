<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk_Asset extends Boom_Controller
{
	public function action_preview()
	{
		$chunk = ORM::factory('Chunk_Asset')
			->values(array(
				'asset_id'	=>	$this->request->query('asset_id'),
				'link'		=>	$this->request->query('url'),
			));

		$chunk = new Chunk_Asset($this->request->query('slotname'), $this->page, $chunk);
		$chunk->template($this->request->query('template'));

		$this->response->body($chunk->execute());
	}

	/**
	 * Save one or more asset slots to a page.
	 */
	public function action_save()
	{
		
	}
}