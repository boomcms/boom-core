<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset controller for video hosted on Bits on the Run
 *
 * @package		BoomCMS
 * @category		Assets
 * @category		Controllers
 * @author		Rob Taylor
 * @copyright		Hoop Associates
 *
 */
class Boom_Controller_Asset_BOTR extends Controller_Asset
{
	public function embed()
	{
		return '<script type="text/javascript" src="http://content.bitsontherun.com/players/'.$this->asset->filename.'-skywOW0k.js"></script>';
	}

	public function action_thumb()
	{
		// Filename of the video thumbnail.
		$thumb = ASSETPATH.$this->_asset->id . ".thumb";

		// If the thumbnail file doesn't exist then sync the asset data.
		if ( ! file_exists($thumb))
		{
			// Save the video thumbnail localy.
			try
			{
				copy("http://content.bitsontherun.com/thumbs/".$this->asset->filename.".jpg", ASSETPATH.$this->asset->id.".thumb");
			}
			catch (Exception $e)
			{
				$thumb = MODPATH.'boom/static/cms/img/icons/40x40/mov_icon.gif';
			}
		}

		$image = Image::factory($thumb);
		$this->response
			->headers('Content-type', 'image/jpeg')
			->body($image->render());
	}

	public function action_view()
	{
		$this->response->body($this->embed());
	}
}
