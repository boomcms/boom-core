<?php

namespace BoomCMS\Core\Asset\Delete;

use \Boom\Asset\Command;
use \Boom\Asset\Asset;

class OldVersions extends Command
{
    public function execute(Asset $asset)
    {
        foreach (glob($asset->getFilename().".*.bak") as $file) {
            unlink($file);
        }
    }
}
