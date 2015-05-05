<?php

namespace BoomCMS\Core\Asset;

abstract class Command
{
    abstract public function execute(Asset $asset);
}
