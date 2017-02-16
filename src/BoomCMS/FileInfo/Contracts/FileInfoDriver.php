<?php

namespace BoomCMS\FileInfo\Contracts;

interface FileInfoDriver
{
    public function getAspectRatio(): float;

    public function getCreatedAt();

    public function getHeight(): float;

    public function getMetadata(): array;

    public function getTitle(): string;

    public function getWidth(): float;
}
