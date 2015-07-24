<?php

namespace BoomCMS\Core\Chunk;

/**
 * Link chunk - this is essentially a linkset which only has one link associated with it.
 *
 */
class Link extends Linkset
{
    protected $defaultTemplate = '';
    protected $html = "<a href='{url}'>{text}</a>";
    protected $type = 'link';

    protected $links;

    public function attributes()
    {
        return [
            $this->attributePrefix . 'text' => $this->getText(),
            $this->attributePrefix . 'url' => (string) $this->getUrl(),
            $this->attributePrefix . 'target_page_id' => $this->getTargetPageId(),
        ];
    }

    public function show()
    {
        return str_replace(['{url}', '{text}'], [$this->getUrl(), $this->getText()], $this->html);
    }

    public function showDefault()
    {
        return str_replace(['{url}', '{text}'], ['#', $this->getPlaceholderText()], $this->html);
    }

    public function getLink()
    {
        if ($this->links === null) {
            if (isset($this->attrs['links'])) {
                $this->links = $this->attrs['links'];

                if (! $this->editable) {
                    foreach ($this->links as $i => $link) {
                        if ($link->isInternal() && ! $link->getLink()->getPage()->isVisible()) {
                            unset($this->links[$i]);
                        }
                    }
                }
            } else {
                $this->links = [];
            }
        }

        return isset($this->links[0]) ? $this->links[0] : null;
    }

    public function getTargetPageId()
    {
        return $this->hasContent() ? $this->getLink()->getTargetPageId() : 0;
    }

    public function getText()
    {
        return $this->hasContent() ? $this->getLink()->getTitle() : '';
    }

    public function getUrl()
    {
        return $this->hasContent() ? $this->getLink()->getTarget()->url() : '';
    }

    public function hasContent()
    {
        return $this->getLink() !== null;
    }
	
	public function setHtml($html)
	{
		$this->html = $html;
		
		return $this;
	}
}
