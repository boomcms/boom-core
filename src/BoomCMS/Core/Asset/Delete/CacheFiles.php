<?php

namespace BoomCMS\Core\Asset\Delete;

use BoomCMS\Core\Asset\Asset;

class CacheFiles extends \Boom\Asset\Command
{
    public function execute(Asset $asset)
    {
        foreach (glob($asset->getFilename()."_*.cache") as $file) {
            unlink($file);
        }
    }
}
