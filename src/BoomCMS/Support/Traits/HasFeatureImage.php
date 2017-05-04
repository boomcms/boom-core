<?php

namespace BoomCMS\Support\Traits;

use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Database\Models\Asset;

trait HasFeatureImage
{
    /**
     * @var AssetInterface
     */
    protected $featureImage = null;

    /**
     * @return null|AssetInterface
     */
    public function getFeatureImage()
    {
        if ($this->featureImage === null) {
            $this->featureImage = $this->belongsTo(Asset::class, $this->getFeatureImageAttributeName())->first();
        }

        return $this->featureImage;
    }

    /**
     * @return int
     */
    public function getFeatureImageId(): int
    {
        return $this->{$this->getFeatureImageAttributeName()} ?? 0;
    }

    public function getFeatureImageAttributeName(): string
    {
        return $this->{self::ATTR_FEATURE_IMAGE};
    }

    /**
     * Whether the model has a feature image defined.
     *
     * @return bool
     */
    public function hasFeatureImage(): bool
    {
        return !empty($this->getFeatureImageId());
    }

    /**
     * @param AssetInterface $asset
     *
     * @return $this
     */
    public function setFeatureImage(AssetInterface $asset)
    {
        $this->setFeatureImageId($asset->getId());

        return $this;
    }

    /**
     * @param int $featureImageId
     *
     * @return $this
     */
    public function setFeatureImageId($featureImageId)
    {
        $this->{$this->getFeatureImageAttributeName()} = $featureImageId > 0 ? $featureImageId : null;

        return $this;
    }
}
