<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset controller for display images.
 *
 * @package		BoomCMS
 * @category		Assets
 * @category		Controllers
 * @author		Rob Taylor
 * @copyright		Hoop Associates
 *
 */
class Boom_Controller_Asset_Image extends Controller_Asset
{
	/**
	 *
	 * @var int
	 */
	public $crop;

	/**
	 *
	 * @var int
	 */
	public $height;

	/**
	 *
	 * @var int
	 */
	public $quality;

	/**
	 *
	 * @var int
	 */
	public $width;

	public function before()
	{
		parent::before();

		// Set some properties from the request paramaters.
		// This use used for resizing / cropping the image.
		$this->width	= $this->request->param('width');
		$this->height	= $this->request->param('height');
		$this->quality	= ($this->request->param('quality'))? $this->request->param('quality') : 100;
		$this->crop	= (bool) $this->request->param('crop');
	}

	public function action_view()
	{
		$filename = $this->asset->get_filename();

		$width = $this->request->param('width');
		$height = $this->request->param('height');
		$crop = (int) $this->request->param('crop');
		$quality = $this->request->param('quality');

		$height = ($height == 0)? $this->asset->height : $height;
		$width = ($width == 0)? $this->asset->width : $width;

		if ($width OR $height OR $crop)
		{
			$filename = $this->asset->create_cache_filename($width, $height, $crop);
			$this->asset->create_cache_file_if_it_doesnt_exist($width, $height, $crop);
		}

		// Load the cached file.
		$image = Image::factory($filename);

		$this->response
			->headers('Content-type', $image->mime)
			->body($image->render(NULL, $quality));
	}

	public function action_embed()
	{
		$this->response->body(HTML::image('asset/view/'.$this->asset->id.'/400'));
	}

	/**
	 * Show a thumbnail of the asset.
	 * For images a thumbnail is just showing an image with different dimensions.
	 */
	public function action_thumb()
	{
		$this->action_view();
	}
}