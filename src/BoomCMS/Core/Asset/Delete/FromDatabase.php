<?php

namespace BoomCMS\Core\Asset\Delete;

use BoomCMS\Core\Asset\Command;
use BoomCMS\Core\Asset\Asset;

class FromDatabase extends Command
{
    public function execute(Asset $asset)
    {
        \DB::delete('assets')
            ->where('id', '=', $asset->getId())
            ->execute();
    }
}
