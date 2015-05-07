<?php

namespace BoomCMS\Core\Asset\Delete;

use BoomCMS\Core\Asset\Command;
use BoomCMS\Core\Asset\Asset;

class File extends Command
{
    public function execute(Asset $asset)
    {
        @unlink($asset->getFilename());
    }
}
