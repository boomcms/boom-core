<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset wrapper for video hosted on Bits on the Run
 *
 * @package Boom
 * @category Assets
 * @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
 * @copyright 2011, Hoop Associates
 *
 */
class Boom_Asset_BOTR extends Asset
{
	public function embed()
	{
		return '<script type="text/javascript" src="http://content.bitsontherun.com/players/' . $this->instance()->filename . '-skywOW0k.js"></script>';
	}

	public function show(Response $response)
	{
		$response->body($this->embed());
	}

	public function preview(Response $response)
	{
		// Filename of the video thumbnail.
		$thumb = ASSETPATH . $this->_asset->id . ".thumb";

		// If the thumbnail file doesn't exist then sync the asset data.
		if ( ! file_exists($thumb))
		{
			// Save the video thumbnail localy.
			try
			{
				copy("http://content.bitsontherun.com/thumbs/" . $this->_asset->filename . ".jpg", ASSETPATH . $this->_asset->id . ".thumb");
			}
			catch (Exception $e)
			{
				$thumb = MODPATH . 'boom/static/cms/img/icons/40x40/mov_icon.gif';
			}
		}

		$image = Image::factory($thumb);
		$response->headers('Content-type', 'image/jpeg');
		$response->body($image->render());
	}
}
