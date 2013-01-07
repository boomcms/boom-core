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
		// TODO: POST would be better for linksets, slideshows already use POST.
		$query = $this->request->query();

		if (isset($query['data']['links']))
		{
			// urldecode() to the link urls
			foreach ($query['data']['links'] as & $link)
			{
				$link['url'] = urldecode($link['url']);
			}

			// Add the links to the linkset model.
			$model->links($query['data']['links']);
		}

		// Create a chunk with the linkset model.
		$chunk = new Chunk_Linkset($this->page, $model, $query['slotname'], TRUE);
		$chunk->template($query['template']);

		// Display the chunk.
		$this->response->body($chunk->execute());
	}
}