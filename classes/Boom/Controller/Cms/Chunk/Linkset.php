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
		// Instantiate a linkset model
		$model = new Model_Chunk_Linkset;

		// Get the linkset data from the query string.
		$data = $this->request->post();

		if (isset($data['data']['links']))
		{
			// urldecode() to the link urls
			foreach ($data['data']['links'] as & $link)
			{
				$link['url'] = urldecode($link['url']);
			}

			// Add the links to the linkset model.
			$model->links($data['data']['links']);
		}

		// Create a chunk with the linkset model.
		$chunk = new Chunk_Linkset($this->page, $model, $data[ 'slotname' ], TRUE);
		$chunk->template($data[ 'template' ]);

		// Display the chunk.
		$this->response->body($chunk->execute());
	}
}
