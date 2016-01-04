<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

class Text extends BaseChunk
{
    use AcceptsHtmlString;

    protected $allowFormatting = false;

    public function __construct(Page $page, array $attrs, $slotname, $editable)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        // Formatting is allowed for bodycopy by default.
        // For other text chunks it must be manually set.
        $this->allowFormatting = ($this->slotname === 'bodycopy');
    }

    public function getHtmlContainerForSlotname($slotname)
    {
        switch ($slotname) {
            case 'standfirst':
                return '<p class="standfirst">{text}</p>';
            case 'bodycopy':
                return '<div class="content">{text}</div>';
            default:
                return $this->allowFormatting ? '<div>{text}</div>' : '<p>{text}</p>';
        }
    }

    public function allowFormatting()
    {
        $this->allowFormatting = true;

        return $this;
    }

    protected function show()
    {
        return $this->showText($this->text());
    }

    protected function showDefault()
    {
        return $this->showText($this->getPlaceholderText());
    }

    public function getPlaceholderText()
    {
        $placeholder = parent::getPlaceholderText();

        return $this->allowFormatting ? "<p>$placeholder</p>" : $placeholder;
    }

    public function hasContent()
    {
        return isset($this->attrs['text'])
            && trim($this->attrs['text']) != null
            && strcmp(strip_tags(trim($this->attrs['text'])), $this->getPlaceholderText()) !== 0;
    }

    private function showText($text)
    {
        $html = $this->html ?: $this->getHtmlContainerForSlotname($this->slotname);

        return str_replace('{text}', $text, $html);
    }

    public function text()
    {
        if ($this->hasContent()) {
            return ($this->isEditable()) ? $this->attrs['text'] : $this->attrs['site_text'];
        }

        return '';
    }
}
