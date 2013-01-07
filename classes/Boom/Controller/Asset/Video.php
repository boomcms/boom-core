<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset controller for displaying videos
 *
 * @package		BoomCMS
 * @category		Assets
 * @category		Controllers
 * @author		Rob Taylor
 * @copyright		Hoop Associates
 *
 */
class Boom_Controller_Asset_Video extends Controller_Asset
{
	public function action_view() {}

	public function action_thumb()
	{
		$image = Image::factory(MODPATH . 'boom/media/img/icons/40x40/mov_icon.gif');

		$this->response
			->headers('Content-type', 'image/gif')
			->body($image->render());
	}
}