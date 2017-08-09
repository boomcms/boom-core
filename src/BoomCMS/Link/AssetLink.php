<?php

namespace BoomCMS\Link;

use BoomCMS\Contracts\Models\Asset;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Helpers\URL;
use Illuminate\Support\Facades\Auth;

class AssetLink extends Internal
{
    /**
     * @var Asset
     */
    protected $asset;

    public function __construct($link, array $attrs = [])
    {
        parent::__construct($link, $attrs);

        if ($link instanceof Asset) {
            $this->asset = $link;
        } elseif(is_numeric($link)) {
            $this->asset = AssetFacade::find($link);
        } else {
            $assetId = URL::getAssetId($link);
            $this->asset = AssetFacade::find($assetId);
        }
    }

    protected function getContentFeatureImageId(): int
    {
        $thumbnailAssetId = $this->asset->getThumbnailAssetId();

        return !empty($thumbnailAssetId) ? $thumbnailAssetId : $this->asset->getId();
    }

    protected function getContentText(): string
    {
        return $this->asset->getDescription();
    }

    protected function getContentTitle(): string
    {
        return $this->asset->getTitle();
    }

    public function getAsset()
    {
        return $this->asset;
    }

    public function isValid(): bool
    {
        return $this->asset !== null;
    }

    public function isVisible(): bool
    {
        return $this->asset->isPublic() || Auth::check();
    }

    public function url()
    {
        return is_string($this->link) && !ctype_digit($this->link) ? 
            $this->link : route('asset', ['asset' => $this->asset]);
    }
}
