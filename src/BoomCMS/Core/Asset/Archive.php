<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Contracts\Models\Asset as AssetInterface;

abstract class Archive
{
    protected $filename;

    abstract public function addAsset(AssetInterface $asset);

    abstract public function close();

    public function delete()
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
