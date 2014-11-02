<?php

namespace Boom\Asset\Delete;

class OldVersions extends \Boom\Asset\Command
{
    public function execute(\Boom\Asset $asset)
    {
        foreach (glob($asset->getFilename().".*.bak") as $file) {
            unlink($file);
        }
    }
}
