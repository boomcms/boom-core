<?php

namespace BoomCMS\Chunk;

use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

/**
 * Link chunk - this is essentially a linkset which only has one link associated with it.
 */
class Link extends Linkset
{
    use AcceptsHtmlString;

    protected $defaultHtml = "<a href='{url}'>{text}</a>";

    /**
     * Whether the 'link text' option should be visible in the link picker.
     *
     * @var bool
     */
    protected $editableText = true;

    protected $link;

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
            $this->attributePrefix.'edittext'       => (int) $this->editableText,
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
        if ($this->link === null) {
            $links = $this->getLinks();

            if (isset($links[0])) {
                $this->link = $links[0];
            }
        }

        return $this->link;
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

    /**
     * Removes the link text option from the link picker.
     *
     * @return $this
     */
    public function noText()
    {
        $this->editableText = false;

        return $this;
    }
}
