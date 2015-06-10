<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page as Page;
use BoomCMS\Core\Link\Link as Link;
use \View as View;

class Asset extends BaseChunk
{
    protected $_asset;
    protected $_default_template = 'image';
    protected $_type = 'asset';

    private $filterByType;
    private $link;

    public function __construct(Page\Page $page, $chunk, $editable = true)
    {
        parent::__construct($page, $chunk, $editable);

        $this->_asset = \Boom\Asset\Factory::fromModel($this->_chunk->target);
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
        return new View($this->viewPrefix."default/asset/$this->template");
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
        return $this->_asset;
    }

    public function getCaption()
    {
        return $this->_chunk->caption;
    }

    public function getLink()
    {
        if ($this->link === null && $this->_chunk->url) {
            $this->link = Link::factory($this->_chunk->url);
        }

        return $this->link;
    }

    public function getTitle()
    {
        return $this->_chunk->title;
    }

    public function hasContent()
    {
        return $this->_chunk->loaded() && $this->_asset->loaded();
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
        return $this->_asset->getId();
    }
}
