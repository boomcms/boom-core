<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Asset controller for displaying PDF files.
 *
 * @package		BoomCMS
 * @category		Assets
 * @category		Controllers
 * @author		Rob Taylor
 * @copyright		Hoop Associates
 *
 */
class Boom_Controller_Asset_PDF extends Controller_Asset
{
	/**
	 * Returns the filename of the thumbnail image for this PDF.
	 *
	 * @return string
	 */
	public function thumbnail()
	{
		// The filename of the asset.
		$filename = ASSETPATH.$this->asset->id;

		// The filename of the asset thumbnail.
		$thumb = $filename.".thumb";

		// Does the thumbnail exist?
		if ( ! file_exists($thumb))
		{
			// No, save the first page of the PDF to an image to create a thumbnail.
			$image = new Imagick($filename.'[0]');
			$image->setImageFormat('jpg');
			$image->writeImage($thumb);

			unset($image);
		}

		// Return the filename of the thumbnail.
		return $thumb;
	}

	public function action_view()
	{
		$this->response
			->headers(array(
				'Content-Type'				=>	'application/pdf',
				'Content-Disposition'			=>	'inline; filename="'.$this->asset->filename.'"',
				'Content-Transfer-Encoding'	=>	'binary',
				'Content-Length'			=>	$this->asset->filesize,
				'Accept-Ranges'				=>	'bytes',
			))
			->body(readfile(ASSETPATH . $this->asset->id));
	}

	public function action_thumb()
	{
		// Create an image object with the PDF's thumbnail file.
		$image = Image::factory($this->thumbnail());

		// Get dimensions etc. from the request parameters.
		$height = $this->request->param('height');
		$width = $this->request->param('width');
		$quality = $this->request->param('quality');

		// Set default values for height, width, and quality if none has been given.
		$height = ($height == 0)? $image->height : $height;
		$width = ($width == 0)? $image->width : $width;
		$quality = ($quality == 0)? NULL : $quality;

		// Is a height or width set for the thumbnail?
		if ($width OR $height)
		{
			// Resize the thumbnail
			$image->resize($width, $height);
		}

		// Display the thumbnail
		$this->response
			->headers('Content-type', 'image/jpg')
			->body($image->render(NULL, $quality));
	}

}
