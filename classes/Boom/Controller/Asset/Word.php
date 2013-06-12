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
class Boom_Controller_Asset_Word extends Controller_Asset
{
	public function action_view()
	{
		$this->response
			->headers(array(
				'Content-Type'				=>	File::mime($this->asset->get_filename()),
				'Content-Disposition'			=>	'inline; filename="'.$this->asset->filename.'"',
				'Content-Transfer-Encoding'	=>	'binary',
				'Content-Length'			=>	$this->asset->filesize,
				'Accept-Ranges'				=>	'bytes',
			))
			->body(readfile($this->asset->get_filename()));
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