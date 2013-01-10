<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * CMS chunk controller.
 * This controller doesn't handle any editing itself - it displays the templates which gives users the ability to edit chunks inline.
 * The chunks are saved through the page save controller.
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk extends Boom_Controller
{
	/**
	 *
 	 * @var	Model_Page	Object representing the current page.
	 */
	protected $page;

	/**
 	 * Load the current page.
	 * All of these methods should be called with a page ID in the params
	 * Before the methods are called we find the page so it can be used, clever eh?
	 *
	 * @return	void
	 */
	public function before()
	{
		parent::before();

		$this->page = new Model_Page($this->request->param('id'));

		// Permissions check
		$this->_authorization('edit_page', $this->page);
	}

	/**
	* Insert a new chunk.
	*
	* **Accepted GET parameters:**
	* Name					|	Type	|	Description
	*-----------------------|-----------|---------------
	* slottype				| 	string 	|	The type of slot being inserted; feature, linkset, etc.
	* slotname				|	string	|	The name of the slot the chunk is going into.
	* template				|	string	|	The template to be used for the chunk.
	* preview_target_rid	|	int		|	Used for feature slots. ID of the target page.
	* data					|	json	|	Used for linksets. json encoded data containing the linkset's links.
	*
	*/
	public function action_insert()
	{
		$template = $this->request->query('template');

		if ($template == 'undefined')
		{
			$template = NULL;
		}

		$chunk = ORM::factory('Chunk');
		$chunk->type = $this->request->query('slottype');
		$chunk->slotname = $this->request->query('slotname');
		$target = 0;

		if ($chunk->type == 'feature')
		{
			$chunk->data->target = ORM::factory('Page', $this->request->query('preview_target_rid'));
			$target = $chunk->data->target;
		}
		elseif ($chunk->type == 'linkset')
		{
			$chunk->data->links(Arr::get($this->request->query('data'), 'links'));
		}
		elseif ($chunk->type == 'tag')
		{
			$chunk->data->tag_id = $target = $this->request->query('preview_target_rid');
		}
		elseif ($chunk->type == 'asset')
		{
			$chunk->data->asset_id = $target = $this->request->query('preview_target_rid');
		}
		elseif ($chunk->type == 'slideshow')
		{
			$slides = Arr::get($this->request->query('data'), 'slides');
			$target = implode("-", Arr::pluck($slides, 0));

			foreach ($slides as & $slide)
			{
				$caption = $slide['caption'];
				$url = $slide['link'];
				$asset_id = $slide['asset_rid'];
				$slide = new Model_Chunk_Slideshow_Slide;
				$slide->asset_id = $asset_id;
				$slide->caption = $caption;

				if ($url != '#')
				{
					$slide->url = $url;
				}
			}

			$chunk->data->slides($slides);
		}

		$output = HTML::chunk_classes($chunk->data->preview($template), $chunk->type, $chunk->slotname, $target, $template, 0, TRUE);

		$this->response->body($output);
	}

	/**
	* Insert an internal link into a text slot.
	* This controller displays the form to select a page to link to.
	*/
	public function action_insert_url()
	{
		$this->template = View::factory('boom/editor/slot/insert_link');
	}
}
