<?php

namespace BoomCMS\Core\Asset\Delete;

use \Boom\Asset\Command;
use \Boom\Asset\Asset;

class File extends Command
{
    public function execute(Asset $asset)
    {
        @unlink($asset->getFilename());
    }
}
