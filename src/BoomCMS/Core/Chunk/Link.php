<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

/**
 * Link chunk - this is essentially a linkset which only has one link associated with it.
 */
class Link extends Linkset
{
    use AcceptsHtmlString;

    protected $defaultHtml = "<a href='{url}'>{text}</a>";

    protected $links;

    public function addContentToHtml($url, $text)
    {
        $html = $this->html ?: $this->defaultHtml;

        return str_replace(['{url}', '{text}'], [$url, $text], $html);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix.'text'           => $this->getText(),
            $this->attributePrefix.'url'            => (string) $this->getUrl(),
            $this->attributePrefix.'target_page_id' => $this->getTargetPageId(),
        ];
    }

    public function show()
    {
        return $this->addContentToHtml($this->getUrl(), $this->getText());
    }

    public function showDefault()
    {
        return $this->addContentToHtml('#', $this->getPlaceholderText());
    }

    public function getLink()
    {
        if ($this->links === null) {
            if (isset($this->attrs['links'])) {
                $this->links = $this->attrs['links'];

                if (!$this->editable) {
                    foreach ($this->links as $i => $link) {
                        if ($link->isInternal() && !$link->getLink()->getPage()->isVisible()) {
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
}
