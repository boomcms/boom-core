<?php

namespace Boom\Asset\Delete;

class CacheFiles extends \Boom\Asset\Command
{
    public function execute(\Boom\Asset $asset)
    {
        foreach (glob($asset->getFilename()."_*.cache") as $file) {
            unlink($file);
        }
    }
}
