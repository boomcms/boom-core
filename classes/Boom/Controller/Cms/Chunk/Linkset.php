<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk_Linkset extends Boom_Controller_Cms_Chunk
{
	public function action_edit()
	{
		$this->template = View::factory('boom/editor/slot/linkset', array(
			'page'	=>	$this->page,
		));
	}

	public function action_preview()
	{
		$model = new Model_Chunk_Linkset;

		$query = $this->request->query();

		if (isset($query['data']['links']))
		{
			// urldecode() to the link urls
			foreach ($query['data']['links'] as & $link)
			{
				$link['url'] = urldecode($link['url']);
			}

			$model->links($query['data']['links']);
		}

		$chunk = new Chunk_Linkset($this->page, $model, $query['slotname'], TRUE);
		$chunk->template($query['template']);

		$this->response->body($chunk->execute());
	}
}