<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset controller for displaying MS Word files.
 *
 * @package		BoomCMS
 * @category	Assets
 * @category	Controllers
 * @author		Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Controller_Asset_MSWord extends Controller_Asset
{
	public function action_view()
	{
		$this->_log_download();
		$this->_do_download();
	}

	public function action_thumb()
	{
		$image = Image::factory(MODPATH.'boom/media/boom/img/icons/ms_word.jpg');
		$image->resize(40, 40);

		$this->response
			->headers('Content-type', 'image/jpg')
			->body($image->render());
	}
}