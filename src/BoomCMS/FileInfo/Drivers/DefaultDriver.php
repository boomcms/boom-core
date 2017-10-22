<?php

namespace BoomCMS\FileInfo\Drivers;

use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Illuminate\Filesystem\FilesystemAdapter;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class DefaultDriver implements FileInfoDriver
{
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $metadata = null;

    /**
     * @var string
     */
    protected $originalFilename;

    /**
     * @var string
     */
    protected $path;

    public function __construct(FilesystemAdapter $filesystem, string $path)
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
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
    public function getAssetType(): string
    {
        return 'doc';
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

    public function getExtension(): string
    {
        $extension = pathinfo($this->getPath(), PATHINFO_EXTENSION);

        if (!empty($extension)) {
            return $extension;
        }

        ExtensionGuesser::getInstance()->guess($this->getMimetype());
    }

    public function getFilename(): string
    {
        return $this->originalFilename ?: basename($this->getPath());
    }

    public function getFilesize(): int
    {
        return $this->filesystem->size($this->getPath());
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

    public function getMimetype(): string
    {
        return $this->filesystem->mimeType($this->getPath());
    }

    public function getPath(): string
    {
        return $this->path;
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
     * This method should be overridden by drivers to provide custom behaviour for reading metadata from a file.
     *
     * @return array
     */
    protected function readMetadata(): array
    {
        return [];
    }

    protected function readStream()
    {
        return $this->filesystem->path($this->path);
    }

    public function setOriginalFilename(string $filename): FileInfoDriver
    {
        $this->originalFilename = $filename;

        return $this;
    }
}
