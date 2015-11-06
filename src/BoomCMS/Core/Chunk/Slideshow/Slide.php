<?php

namespace BoomCMS\Core\Chunk\Slideshow;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Core\Link\Link;
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

        if (isset($this->attrs['asset'])) {
            $this->asset = new Asset($this->attrs['asset']);
        }
    }

    public function getAsset()
    {
        if ($this->asset === null) {
            $this->asset = AssetFacade::findById($this->attrs['asset_id']);
        }

        return $this->asset;
    }

    public function getAssetId()
    {
        return $this->getAsset()->getId();
    }

    public function getCaption()
    {
        return isset($this->attrs['caption']) ? $this->attrs['caption'] : '';
    }

    public function getId()
    {
        return $this->attrs['id'];
    }

    /**
     * @return Link
     */
    public function getLink()
    {
        return Link::factory($this->attrs['url']);
    }

    public function getLinktext()
    {
        return isset($this->attrs['link_text']) ? $this->attrs['link_text'] : '';
    }

    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
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
