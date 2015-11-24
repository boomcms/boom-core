<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use Imagick;
use InvalidArgumentException;

/**
 * Creates a thumbnail image for a PDF.
 */
class PdfThumbnail
{
    /**
     * @var AssetInterface
     */
    protected $asset;

    /**
     * @param AssetInterface $asset
     */
    public function __construct(AssetInterface $asset)
    {
        $this->asset = $asset;

        if ($this->asset->getExtension() !== 'pdf') {
            throw new InvalidArgumentException('Given asset is not a PDF');
        }
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->getFilename());
    }

    public function generate()
    {
        $image = new Imagick($this->asset->getFilename().'[0]');
        $image->setImageFormat('png');

        file_put_contents($this->getFilename(), $image->getImageBlob());
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->asset->getFilename().'.thumb';
    }

    public function getAndMakeFilename()
    {
        if (!$this->exists()) {
            $this->generate();
        }

        return $this->getFilename();
    }
}
