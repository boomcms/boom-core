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

    /**
     * @param array $attrs
     */
    public function __construct($attrs)
    {
        $this->attrs = $attrs;

        if (isset($attrs['link'])) {
            $this->link = $attrs['link'];
        }
    }

    /**
     * Returns the ID of the link's associated asset.
     *
     * @return int
     */
    public function getAssetId()
    {
        return $this->attrs['asset_id'];
    }

    /**
     * Returns the database ID of the link.
     *
     * @return int
     */
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

    /**
     * Alias for getLink().
     *
     * @return Link
     */
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

            return $this->attrs['text'] = $page ? Chunk::get('text', 'standfirst', $page)->text() : '';
        }

        return $this->attrs['text'] = '';
    }

    /**
     * Returns the contents of the text attribute.
     *
     * @return string
     */
    public function getTextAttribute()
    {
        return isset($this->attrs['text']) ? $this->attrs['text'] : '';
    }

    /**
     * Returns the link title.
     *
     * @return string
     */
    public function getTitle()
    {
        return (isset($this->attrs['title']) && $this->attrs['title']) ?
            $this->attrs['title'] :
            $this->getLink()->getTitle();
    }

    /**
     * Returns the link URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->attrs['url'];
    }

    /**
     * Returns true if the link is an  internal link.
     *
     * @return bool
     */
    public function isInternal()
    {
        return $this->getLink()->isInternal();
    }

    /**
     * Returns true if the link is an external link.
     *
     * @return bool
     */
    public function isExternal()
    {
        return !$this->isInternal();
    }
}
