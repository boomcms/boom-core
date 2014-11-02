<?php

namespace Boom\Asset\Delete;

use \Boom\Asset\Command;
use \Boom\Asset\Asset;

class FromDatabase extends Command
{
    public function execute(Asset $asset)
    {
        \DB::delete('assets')
            ->where('id', '=', $asset->getId())
            ->execute();
    }
}
