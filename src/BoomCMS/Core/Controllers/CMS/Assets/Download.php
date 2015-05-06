<?php

use Boom\Asset;

class Controller_Cms_Assets_Download extends Controller_Cms_Assets
{
    /**
	 *
	 * @var array
	 */
    public $asset_ids = [];

    public function before()
    {
        parent::before();

        $this->asset_ids = $this->request->param('asset_ids');
    }

    public function single()
    {
        $asset = Asset\Factory::byId($this->asset_ids[0]);

        if ( ! $asset->exists()) {
            throw new HTTP_Exception_404();
        }

        $processorClassName = 'Boom\\Asset\\Processor\\' . class_basename($asset);
        $processor = new $processorClassName($asset, $this->response);

        $this->response = $processor->download();
    }

    /**
	 * Download multiple assets
	 *
	 * Creates a .zip file containing the specified assets.
	 *
	 * @uses ZipArchive
	 */
    public function multiple()
    {
        $zip = new Asset\Archive\Zip();

        foreach ($this->asset_ids as $assetId) {
            $zip->addAsset(Asset\Factory::byId($assetId));
        }

        $zip->close();

        $this->response
            ->headers([
                "Content-type"            =>    "application/zip",
                "Content-Disposition"    =>    "attachment; filename=cms_assets.zip",
                "Pragma"                =>    "no-cache",
                "Expires"                =>    "0"
            ])
            ->body(readfile($zip->getFilename()));

        $zip->delete();
    }

    public function after() {}
}
