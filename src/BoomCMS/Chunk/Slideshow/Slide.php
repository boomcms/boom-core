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

        if (isset($this->attrs['asset'])) {
            if (is_array($this->attrs['asset'])) {
                $this->asset = new Asset($this->attrs['asset']);
                $this->asset->{Asset::ATTR_ID} = $this->attrs['asset'][Asset::ATTR_ID];

                return;
            }

            $this->asset = $this->attrs['asset'];
        }
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
        return isset($this->attrs['caption']) ? $this->attrs['caption'] : '';
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
