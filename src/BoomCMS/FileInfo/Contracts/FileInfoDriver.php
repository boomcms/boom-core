<?php

namespace BoomCMS\FileInfo\Contracts;

use Carbon\Carbon;

interface FileInfoDriver
{
    public function getAspectRatio(): float;

    public function getCreatedAt(): Carbon;

    public function getHeight(): float;

    public function getMetadata(): array;

    public function getWidth(): float;
}
