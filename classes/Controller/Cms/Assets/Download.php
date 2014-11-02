<?php

use Boom\Asset;
use \Boom\Asset\Finder as Finder;

class Controller_Cms_Assets_Download extends Controller_Cms_Assets
{
	/**
	 *
	 * @var array
	 */
	public $asset_ids = array();

	public function before()
	{
		parent::before();

		$this->asset_ids = $this->request->param('asset_ids');
	}

	public function action_single()
	{
		$asset = Finder::byId($this->asset_ids[0]);

		if ( ! $asset->exists()) {
			throw new HTTP_Exception_404;
		}

		$this->response
			->headers(array(
				"Content-type" => (string) $asset->getMimetype(),
				"Pragma" => "no-cache",
				"Expires" => "0"
			))
			->body(
				readfile($asset->getFilename())
			);

		if ( ! $asset instanceof Asset\Type\Image) {
			$this->response->headers('Content-Disposition', 'attachment; filename='.basename($asset->getOriginalFilename()));
		}
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
		$zip = new Asset\Archive\Zip;

		foreach ($this->asset_ids as $assetId)
		{
			$zip->addAsset(Finder::byId($assetId));
		}

		$zip->close();

		$this->response
			->headers(array(
				"Content-type"			=>	"application/zip",
				"Content-Disposition"	=>	"attachment; filename=cms_assets.zip",
				"Pragma"				=>	"no-cache",
				"Expires"				=>	"0"
			))
			->body(readfile($zip->getFilename()));

		$zip->delete();
	}

	public function after() {}
}