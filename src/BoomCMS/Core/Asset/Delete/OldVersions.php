<?php

namespace BoomCMS\Core\Asset\Delete;

use BoomCMS\Core\Asset\Command;
use BoomCMS\Core\Asset\Asset;

class OldVersions extends Command
{
    public function execute(Asset $asset)
    {
        foreach (glob($asset->getFilename().".*.bak") as $file) {
            unlink($file);
        }
    }
}
