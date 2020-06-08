<?php

namespace BoomCMS\Chunk\Slideshow;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Link\Link;
use BoomCMS\Support\Facades\Asset as AssetFacade;

class Slide
{
    /**
     * @var array
     */
    protected $attrs;

    /**
     * @var Asset
     */
    protected $asset;

    public function __construct($attrs)
    {
        $this->attrs = $attrs;

        // Prevent asset from being restored from cache.
        // Fixes issue when an asset is included in a cached slideshow but deleted via the asset manager.
        $this->attrs['asset'] = null;
    }

    public function getAsset()
    {
        if ($this->asset === null) {
            $this->asset = AssetFacade::find($this->attrs['asset_id']);
        }

        return $this->asset;
    }

    public function getAssetId()
    {
        return $this->attrs['asset_id'];
    }

    public function getCaption()
    {
        return isset($this->attrs['caption']) ? htmlspecialchars($this->attrs['caption']) : '';
    }

    public function getId()
    {
        return $this->attrs['id'];
    }

    /**
     * @return Link|null
     */
    public function getLink()
    {
        return isset($this->attrs['url']) ? Link::factory($this->attrs['url']) : null;
    }

    public function getLinktext()
    {
        return $this->attrs['link_text'] ?? '';
    }

    public function getTitle()
    {
        return $this->attrs['title'] ?? '';
    }

    /**
     * Whether the current slide has a link associated with it.
     *
     * @return bool
     */
    public function hasLink()
    {
        return isset($this->attrs['url'])
            && trim($this->attrs['url'])
            && $this->attrs['url'] !== '#'
            && $this->attrs['url'] !== 'http://';
    }
}
