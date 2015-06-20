<?php

namespace BoomCMS\Core\Chunk;

class Text extends BaseChunk
{
    protected $html;
    protected $type = 'text';
    protected $placeholder;

    public function getHtmlContainerForSlotname($slotname)
    {
        switch ($slotname) {
            case 'standfirst':
                return "<p class=\"standfirst\">{text}</p>";
            case 'bodycopy':
                return "<div class=\"content\">{text}</div>";
            default:
                return "<p>{text}</p>";
        }
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
        if ($this->placeholder !== null) {
            return $this->plaeholder;
        }

        return parent::getPlaceholderText();
    }

    /**
     * Returns the chunk's text without any filters applied
     *
     * @return string
     */
    public function getUnfilteredText()
    {
        return isset($this->attrs['text']) ? $this->attrs['text'] : '';
    }

    public function get_paragraphs($offset = 0, $length = null)
    {
        preg_match_all('|<p>(.*?)</p>|', $this->getUnfilteredText(), $matches, PREG_PATTERN_ORDER);

        return $matches[1];
    }

    public function hasContent()
    {
        return isset($this->attrs['text']) && trim($this->attrs['text']) != null;
    }

    /**
     * Sets the placeholder text that should be shown in the editor if the text chunk has no content.
     *
     * This is useful as a way of setting some text which describes to editors what the text chunk is intended to be used for.
     *
     * @param  string           $text
     * @return \Boom\Chunk\Text
     */
    public function setPlaceholder($text)
    {
        $this->placeholder = $text;

        return $this;
    }

    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
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
