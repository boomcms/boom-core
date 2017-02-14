<?php

namespace BoomCMS\FileInfo\Drivers;

use BoomCMS\FileInfo\Contracts\FileInfoDriver;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\File;

class DefaultDriver implements FileInfoDriver
{
    protected $file;

    protected $metadata = null;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getAspectRatio(): float
    {
        if (!$this->getHeight()) {
            return 1;
        }

        return $this->getWidth() / $this->getHeight();
    }

    public function getCreatedAt(): Carbon
    {
        return null;
    }

    public function getHeight(): int
    {
        return 0;
    }

    public function getMetadata(): array
    {
        if ($this->metadata === null) {
            $this->metadata = $this->readMetadata();
        }

        return $this->metadata;
    }

    public function getPathname(): string
    {
        return $this->file->getPathname();
    }

    public function getWidth(): int
    {
        return 0;
    }

    protected function readMetadata(): array
    {
        return [];
    }
}

