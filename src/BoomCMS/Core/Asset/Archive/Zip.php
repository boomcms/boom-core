<?php

namespace BoomCMS\Core\Asset\Archive;

use Boom;
use BoomCMS\Core\Asset\Asset;
use BoomCMS\Core\Asset\Archive as Archive;
use \ZipArchive as ZipArchive;

/**
 * Creates a ZIP of asset files
 *
 */
class Zip extends Archive
{
    protected $filename;

    /**
	 *
	 * @var Zip
	 */
    protected $zip;

    public function __construct()
    {
        $this->filename = $this->generateUniqueFilename();
        $this->zip = new ZipArchive();
        $this->zip->open($this->filename, ZipArchive::CREATE);
    }

    public function addAsset(Asset $asset)
    {
        if ($asset->exists()) {
            $this->zip->addFile($asset->getFilename(), $asset->getOriginalFilename());
        }
    }

    public function close()
    {
        $this->zip->close();
    }

    protected function generateUniqueFilename()
    {
        do {
            $filename = Boom\Boom::instance()->getCacheDir() . 'asset_archive_' . time() . rand() . '.zip';
        } while (file_exists($filename));

        return $filename;
    }
}
