<?php

namespace BoomCMS\FileInfo\Contracts;

interface FileInfoDriver
{
    /**
     * Returns the aspect ratio of the file
     * 
     * If the file doesn't have an aspect ratio (e.g. documents) then 0 is returned
     *
     * @return float
     */
    public function getAspectRatio(): float;

    /**
     * Returns the date and time that the file was created
     *
     * Returns null if the created time can't be determined
     *
     * @return null|Carbon
     */
    public function getCreatedAt();

    /**
     * Returns the height of the file, or 0 if it doesn't have a height
     *
     * @return float
     */
    public function getHeight(): float;

    /**
     * Returns an associative array of file metadata
     *
     * @return array
     */
    public function getMetadata(): array;

    /**
     * Returns the title from the file metadata, if present
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns the width of the file, or 0 if it doesn't have a width
     *
     * @return float
     */
    public function getWidth(): float;
}
