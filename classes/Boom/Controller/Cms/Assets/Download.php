<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset download controller.
 *
 * Has functions for downloading a single asset or multiple assets as a zip file.
 *
 * A route is used (declared in init.php) to determine whether the single or multiple function should be used
 * Depending on the number of asset IDs given.
 *
 *
 * @package	BoomCMS
 * @category	Assets
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Assets_Download extends Controller_Cms_Assets
{
	/**
	 * Array of asset IDs to be downloaded.
	 *
	 * @var array
	 */
	public $asset_ids = array();

	public function before()
	{
		parent::before();

		$this->asset_ids = $this->request->param('asset_ids');
	}

	/**
	 * Download a single asset.
	 *
	 */
	public function action_single()
	{
		// Get the ID of the asset to download.
		// When downloading a single asset the asset IDs will still be an array but will only contain one element.
		$asset_id = $this->asset_ids[0];

		// Load the asset from the database to check that it exists.
		$this->asset
			->where('id', '=', $asset_id)
			->find();

		// If the asset doesn't exist then do a 404.
		if ( ! $this->asset->loaded())
		{
			throw new HTTP_Exception_404;
		}

		// Asset exists, send the file contents.
		$this->response
			->headers(array(
				"Content-type"			=>	$this->asset->get_mime(),
				"Content-Disposition"	=>	"attachment; filename=".basename($this->asset->get_filename()),
				"Pragma"				=>	"no-cache",
				"Expires"				=>	"0"
			))
			->body(
				readfile($this->asset->get_filename())
			);
	}

	/**
	 * Download multiple assets
	 *
	 * Creates a .zip file containing the specified assets.
	 *
	 * @uses ZipArchive
	 */
	public function action_multiple()
	{
		// The name of the temporary file where the zip archive will be created.
		$tmp_filename = APPPATH.'cache/cms_assets_'.Session::instance()->id().".".$_SERVER['REQUEST_TIME'].'file.zip';

		// Create the zip archive.
		$zip = new ZipArchive;
		$zip->open($tmp_filename, ZipArchive::CREATE);

		// Add the assets to the zip archive
		foreach ($this->asset_ids as $asset_id)
		{
			// Load the asset from the database to check that it exists.
			$this->asset
				->where('id', '=', $asset_id)
				->find();

			if ($this->asset->loaded())
			{
				// Asset exists add it to the archive.
				$zip->addFile($this->asset->get_filename(), $this->asset->get_filename());
			}

			$this->asset->clear();
		}

		// Finished adding files to the archive.
		$zip->close();

		// Send it to the user's browser.
		$this->response
			->headers(array(
				"Content-type"			=>	"application/zip",
				"Content-Disposition"	=>	"attachment; filename=cms_assets.zip",
				"Pragma"				=>	"no-cache",
				"Expires"				=>	"0"
			))
			->body(
				readfile($tmp_filename)
			);

		// Delete the temporary file.
		unlink($tmp_filename);
	}
}