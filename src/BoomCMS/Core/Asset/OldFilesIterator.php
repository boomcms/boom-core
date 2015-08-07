<?php

namespace BoomCMS\Core\Asset;

class OldFilesIterator extends \FilterIterator
{
    /**
     * @var Asset
     */
    protected $asset;

    public function __construct(Asset $asset)
    {
        parent::__construct(new \DirectoryIterator(Asset::directory()));

        $this->asset = $asset;
    }

    public function accept()
    {
        $file = $this->getInnerIterator()->current();

        return preg_match("|{$this->asset->getId()}\.\d+\.bak|", $file->getFilename());
    }
}
