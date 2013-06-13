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
class Boom_Controller_Asset_Pdf extends Controller_Asset
{
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
			->body(readfile($this->asset->get_filename()));
	}

	public function action_thumb()
	{
		// The filename of the asset.
		$filename = $this->asset->get_filename();

		// Thumbnail dimensions.
		$width = $this->request->param('width');
		$height = $this->request->param('height');

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

		$image = Image::factory($thumb);

		if ($width OR $height)
		{
			$image->resize($width, $height);
		}

		// Display the thumbnail
		$this->response
			->headers('Content-type', 'image/jpg')
			->body($image->render());
	}

}
