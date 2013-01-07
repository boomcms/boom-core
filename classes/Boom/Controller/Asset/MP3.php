<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset controller for 'displaying' MP3s
 *
 * @package		BoomCMS
 * @category		Assets
 * @category		Controllers
 * @author		Rob Taylor
 * @copyright		Hoop Associates
 *
 */
class Boom_Controller_Asset_MP3 extends Controller_Asset
{
	public function action_preview()
	{
		$image = Image::factory(MODPATH . 'boom/media/img/icons/40x40/default_icon.gif');

		$this->response
			->headers('Content-type', 'image/gif')
			->body($image->render());
	}

	public function action_view()
	{
		$this->response
			->headers(array(
				'Content-type'		=>	'audio/mpeg',
				'Content-Length'	=>	filesize(ASSETPATH.$this->asset->id),
			))
			->body(readfile($this->asset->id));
	}
}