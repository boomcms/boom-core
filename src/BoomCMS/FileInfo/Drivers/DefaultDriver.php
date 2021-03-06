<?php

namespace BoomCMS\FileInfo\Drivers;

use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Symfony\Component\HttpFoundation\File\File;

class DefaultDriver implements FileInfoDriver
{
    /**
     * @var File
     */
    protected $file;

    /**
     * @var array
     */
    protected $metadata = null;

    /**
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    public function getAspectRatio(): float
    {
        if (empty($this->getHeight())) {
            return 0;
        }

        return $this->getWidth() / $this->getHeight();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCopyright(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    public function getHeight(): float
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getMetadata(): array
    {
        if ($this->metadata === null) {
            $this->metadata = $this->readMetadata();

            ksort($this->metadata);
        }

        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @return Imagick
     */
    public function getThumbnail()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    public function getWidth(): float
    {
        return 0;
    }

    /**
     * Loop through an array of metadata keys and return the first one that exists.
     *
     * @param array $keys
     * @param mixed $default
     *
     * @return mixed
     */
    protected function oneOf(array $keys, $default = null)
    {
        $metadata = $this->getMetadata();

        foreach ($keys as $key) {
            if (isset($metadata[$key])) {
                return $metadata[$key];
            }
        }

        return $default;
    }

    /**
     * This method should be overridden by drivers to provide custom behavior for reading metadata from a file.
     *
     * @return array
     */
    protected function readMetadata(): array
    {
        return [];
    }
}
