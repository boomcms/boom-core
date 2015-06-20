<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page as Page;
use BoomCMS\Core\Link\Link as Link;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class Asset extends BaseChunk
{
    protected $asset;
    protected $defaultTemplate = 'image';
    protected $type = 'asset';

    private $filterByType;
    private $link;

    public function __construct(Page\Page $page, array $attrs, $slotname, $editable = true)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        if (isset($attrs['asset_id'])) {
            $provider = App::make('BoomCMS\Core\Asset\Provider');
            $this->asset = $provider->findById($this->attrs['asset_id']);
        }
    }

    protected function show()
    {
        $link = $this->getLink();

        return View::make($this->viewPrefix."asset/$this->template", [
            'asset' => $this->asset(),
            'caption' => $this->getCaption(),
            'title' => $this->getTitle(),
            'link' => $link
        ]);

        if ($link) {
            $v->set([
                'url' => $link->url()
            ]);
        }

        return $v;
    }

    protected function showDefault()
    {
        return View::make($this->viewPrefix."default/asset/$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ]);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix . 'target' => $this->target(),
            $this->attributePrefix . 'filterByType' => $this->filterByType
        ];
    }

    public function asset()
    {
        return $this->asset;
    }

    public function getCaption()
    {
        return isset($this->attrs['caption'])? $this->attrs['caption'] : '';
    }

    public function getLink()
    {
        if ($this->link === null && isset($this->attrs['url'])) {
            $this->link = Link::factory($this->attrs['url']);
        }

        return $this->link;
    }

    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
    }

    public function hasContent()
    {
        return $this->asset && $this->asset->loaded();
    }

    /**
     * Set which type of asset can be put into the chunk.
     * No validation is done but by default the asset picker will filter by this type.
     *
     * @param string $type An asset type. e.g. 'pdf', 'image'.
     */
    public function setFilterByType($type)
    {
        $this->filterByType = $type;

        return $this;
    }

    public function target()
    {
        return $this->asset && $this->asset->getId();
    }
}
