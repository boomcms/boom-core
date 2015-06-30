<?php

namespace BoomCMS\Core\Chunk\Linkset;

use BoomCMS\Core\Link\Link as LinkObject;

class Link
{
    /**
     *
     * @var array
     */
    protected $attrs;

    public function __construct($attrs)
    {
        $this->attrs = $attrs;
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
        return LinkObject::factory($this->attrs['url']);
    }

    public function getTarget()
    {
        return $this->getLink();
    }

    public function getTitle()
    {
        return (isset($this->attrs['title']) && $this->attrs['title']) ?
            $this->attrs['title'] :
            $this->getLink()->getTitle();
    }

    public function isInternal()
    {
        return $this->getLink()->isInternal();
    }

    public function isExternal()
    {
        return ! $this->isInternal();
    }
}