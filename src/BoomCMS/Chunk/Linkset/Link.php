<?php

namespace BoomCMS\Chunk\Linkset;

use BoomCMS\Link\Link as LinkObject;
use BoomCMS\Support\Facades\Chunk;

class Link
{
    /**
     * @var array
     */
    protected $attrs;

    /**
     * @var Link
     */
    protected $link;

    public function __construct($attrs)
    {
        $this->attrs = $attrs;

        if (isset($attrs['link'])) {
            $this->link = $attrs['link'];
        }
    }

    public function getAssetId()
    {
        return $this->attrs['asset_id'];
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
        if ($this->link === null) {
            $this->link = LinkObject::factory($this->getUrl());
        }

        return $this->link;
    }

    public function getTarget()
    {
        return $this->getLink();
    }

    public function getTargetPageId()
    {
        return $this->attrs['target_page_id'];
    }

    /**
     * Get the text for the link.
     *
     * If text has been given then that is used.
     *
     * Otherwise, if the link is internal, then the standfirst of the linked page is returned.
     *
     * @return string
     */
    public function getText()
    {
        if (isset($this->attrs['text'])) {
            return $this->attrs['text'];
        }

        if ($this->getLink()->isInternal()) {
            $page = $this->getLink()->getPage();

            return $this->attrs['text'] = $page ? Chunk::get('text', 'standfirst', $page) : '';
        }

        return $this->attrs['text'] = '';
    }

    /**
     * Returns the contents of the text attribute
     *
     * @return string
     */
    public function getTextAttribute()
    {
        return isset($this->attrs['text']) ? $this->attrs['text'] : '';
    }

    public function getTitle()
    {
        return (isset($this->attrs['title']) && $this->attrs['title']) ?
            $this->attrs['title'] :
            $this->getLink()->getTitle();
    }

    public function getUrl()
    {
        return $this->attrs['url'];
    }

    public function isInternal()
    {
        return $this->getLink()->isInternal();
    }

    public function isExternal()
    {
        return !$this->isInternal();
    }
}
