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
	public function action_view()
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $this->asset->get_filename());
		finfo_close($finfo);

		$this->response
			->send_file($this->asset->get_filename(), $this->asset->filename, array(
				'inline'		=>	TRUE,
				'mime_type'	=>	$mime,
				'resumable'	=>	TRUE,
			));
	}

	public function action_thumb()
	{
		$filename = ($this->asset->thumbnail_asset_id)?
			$this->asset->thumbnail->get_filename() :
			MODPATH.'boom/media/boom/img/icons/40x40/mov_icon.gif';

		$image = Image::factory($this->asset->thumbnail())
			->resize($this->request->param('width'), $this->request->param('height'));

		$this->response
			->headers('Content-type', $image->mime)
			->body($image->render());
	}
}