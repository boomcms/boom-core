<?php

namespace BoomCMS\FileInfo\Contracts;

use Imagick;

interface FileInfoDriver
{
    /**
     * Returns the aspect ratio of the file.
     *
     * If the file doesn't have an aspect ratio (e.g. documents) then 0 is returned
     *
     * @return float
     */
    public function getAspectRatio(): float;

    /**
     * Returns the type of asset, either doc, image, video, or audio.
     *
     * @return string
     */
    public function getAssetType(): string;

    /**
     * Returns the date and time that the file was created.
     *
     * Returns null if the created time can't be determined
     *
     * @return null|Carbon
     */
    public function getCreatedAt();

    /**
     * Return copyright information from the file metadata, if available.
     *
     * @return string
     */
    public function getCopyright(): string;

    /**
     * Return a description of the file, if available.
     *
     * @return string
     */
    public function getDescription(): string;

    public function getExtension(): string;

    public function getFilename(): string;

    public function getFilesize(): int;

    /**
     * Returns the height of the file, or 0 if it doesn't have a height.
     *
     * @return float
     */
    public function getHeight(): float;

    /**
     * Returns an associative array of file metadata.
     *
     * @return array
     */
    public function getMetadata(): array;

    public function getMimetype(): string;

    public function getPath(): string;

    /**
     * Returns an Imagick object representing a thumbnail for the file, or null.
     *
     * @return null|Imagick
     */
    public function getThumbnail();

    /**
     * Returns the title from the file metadata, if present.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns the width of the file, or 0 if it doesn't have a width.
     *
     * @return float
     */
    public function getWidth(): float;

    public function setOriginalFilename(string $filename): FileInfoDriver;
}
