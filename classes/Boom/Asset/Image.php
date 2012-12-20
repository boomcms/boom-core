<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Image decorator for assets.
 * Handles displaying image assets
 *
 * @package	BoomCMS
 * @category	Assets
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Asset_Image extends Sledge_Asset
{
	public function show(Response $response, $width = NULL, $height = NULL, $quality = NULL, $crop = FALSE)
	{
		// Check that the image exists.
		if ( ! file_exists(ASSETPATH . $this->_asset->id))
		{
			throw new HTTP_Exception_404;
		}

		$filename = ASSETPATH . $this->_asset->id;

		if ($version = Request::current()->query('version'))
		{
			$filename .= "_" . $version . "_";
		}

		if ($width OR $height)
		{
			$filename .= "_" . (int) $width . "_". (int) $height . ".cache" ;
		}

		if ( ! file_exists($filename))
		{
			$image = ($version)? Image::factory(ASSETPATH . $this->_asset->id . ".$version.bak") : Image::factory(ASSETPATH . $this->_asset->id);

			// Set the dimensions and quality of the image.
			$height = ($height == 0)? $image->height : $height;
			$width = ($width == 0)? $image->width : $width;

			if ($width OR $height)
			{
				if ($crop)
				{
					$image->resize($width, $height, Image::INVERSE);
					$image->crop($width, $height);
				}
				else
				{
					$image->resize($width, $height);
				}
			}

			// Save the file.
			// $image->save() doesn't always work with Imagemagick but this does the job.
			file_put_contents($filename, $image->render());
		}
		else
		{
			// Load the cached file.
			$image =  Image::factory($filename);
		}

		$response->headers('Content-type', $image->mime);
		$response->body($image->render(NULL, $quality));
	}

	public function preview(Response $response, $width = NULL, $height = NULL, $quality = NULL, $crop = FALSE)
	{
		return $this->show($response, $width, $height, $quality, $crop);
	}
}