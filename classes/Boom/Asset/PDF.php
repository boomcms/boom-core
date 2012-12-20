<?php defined('SYSPATH') OR die('No direct script access.');
/**
* PDF decorator for assets.
*
* @package Boom
* @category Assets
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Boom_Asset_PDF extends Asset
{
	/**
	 * Returns the filename of the thumbnail image for this PDF.
	 *
	 * @return string
	 */
	public function thumbnail()
	{
		$file = ASSETPATH . $this->_asset->id;
		$thumb = $file . ".thumb";

		if ( ! file_exists($thumb))
		{
			$image = new Imagick($file . '[0]');
			$image->setImageFormat('jpg');
			$image->writeImage($thumb);

			unset($image);
		}

		return $thumb;
	}

	public function show (Response $response)
	{
		$response->headers('Content-type', 'application/pdf');
		$response->headers('Content-Disposition', 'inline; filename="' . $this->_asset->filename . '"');
		$response->headers('Content-Transfer-Encoding', 'binary');
		$response->headers('Content-Length', $this->_asset->filesize);
		$response->headers('Accept-Ranges', 'bytes');

		$response->body(readfile(ASSETPATH . $this->_asset->id));
	}

	public function preview (Response $response, $width = NULL, $height = NULL, $quality = NULL)
	{
		$image = Image::factory($this->thumbnail());

		$height = ($height == 0)? $image->height : $height;
		$width = ($width == 0)? $image->width : $width;
		$quality = ($quality == 0)? NULL : $quality;

		if ($width OR $height)
		{
			$image->resize($width, $height);
		}

		$response->headers('Content-type', 'image/jpg');
		$response->body($image->render(NULL, $quality));
	}

}
