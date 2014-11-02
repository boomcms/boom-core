<?php

namespace Boom\Asset;

use \Boom\Asset as Asset;

class Commander
{
    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function addCommand(\Boom\Asset\Command $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    public function execute()
    {
        if ($this->asset->loaded()) {
            foreach ($this->commands as $command) {
                $command->execute($this->asset);
            }
        }
    }
}
