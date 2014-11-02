<?php

namespace Boom\Chunk;

use \Boom\Page as Page;
use Page\Finder as PageFinder;
use \Boom\Link\Link as Link;
use \View as View;

class Asset extends \Boom\Chunk
{
    protected $_asset;
    protected $_default_template = 'image';
    protected $_type = 'asset';
    private $link;

    public function __construct(Page\Page $page, $chunk, $editable = true)
    {
        parent::__construct($page, $chunk, $editable);

        $this->_asset = \Boom\Asset\Factory::fromModel($this->_chunk->target);
    }

    protected function _show()
    {
        $link = $this->getLink();

        $v = new View($this->viewDirectory."asset/$this->_template", array(
            'asset' => $this->asset(),
            'caption' => $this->getCaption()
        ));

        if ($link) {
            $v->set(array(
                'title' => $link->getTitle(),
                'url' => $link->url()
            ));
        }

        return $v;
    }

    protected function _show_default()
    {
        return new View($this->viewDirectory."default/asset/$this->_template");
    }

    public function attributes()
    {
        return array(
            $this->attributePrefix.'target' => $this->target(),
        );
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

    public function hasContent()
    {
        return $this->_chunk->loaded() && $this->_asset->loaded();
    }

    public function target()
    {
        return $this->_asset->getId();
    }
}
