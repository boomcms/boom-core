<?php

namespace BoomCMS\Contracts\Models;

interface Album
{
    /**
     * @param Asset $asset
     *
     * @return $this
     */
    public function addAsset(Asset $asset);

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
     * @return Site
     */
    public function getSite();

    /**
     * @param Asset $asset
     *
     * @return $this
     */
    public function removeAsset(Asset $asset);

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function setSite(Site $site);
}
