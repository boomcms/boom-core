<?php

namespace BoomCMS\Contracts\Models;

interface Album
{
    /**
     * Add assets to the album.
     *
     * @param array $assetIds
     *
     * @return $this
     */
    public function addAssets(array $assetIds): self;

    /**
     * Updates the count of assets in this album and the featured asset.
     *
     * @return Album $this
     */
    public function assetsUpdated(): self;

    /**
     * @return Asset
     */
    public function getFeatureImage();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return string
     */
    public function getName();

    /**
     * Remove assets from the album.
     *
     * @param array $assetIds
     *
     * @return $this
     */
    public function removeAssets(array $assetIds): self;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);
}
