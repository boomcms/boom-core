<?php

namespace Boom\Asset;

abstract class Command
{
    abstract public function execute(Asset $asset);
}
