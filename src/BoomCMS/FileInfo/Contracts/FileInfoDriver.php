<?php

namespace BoomCMS\FileInfo\Contracts;

use Carbon\Carbon;

interface FileInfoDriver
{
    public function getAspectRatio(): float;

    public function getCreatedAt(): Carbon;

    public function getHeight(): int;

    public function getMetadata(): array;

    public function getPathname(): string;

    public function getWidth(): int;
}