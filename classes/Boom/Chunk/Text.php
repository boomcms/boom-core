<?php

namespace Boom\Chunk;

use Boom\Editor\Editor as Editor;
use Boom\TextFilter\Commander as TextFilter;
use Boom\TextFilter\Filter as Filter;
use \Kohana as Kohana;
use \View as View;

class Text extends \Boom\Chunk
{
    protected $_type = 'text';

    protected function _add_html($text)
    {
        switch ($this->_chunk->slotname) {
            case 'standfirst':
                return "<p class=\"standFirst\">$text</p>";
            case 'bodycopy':
                return "<div id=\"content\">$text</div>";
            case 'bodycopy2':
                return "<div id=\"content-secondary\">$text</div>";
            default:
                return "<p>$text</p>";
        }
    }

    /**
	 *
	 * @uses Model_Chunk_Text::unmunge()
	 * @uses Chunk_Text::embed_video()
	 */
    protected function _show()
    {
        $text = $this->text();

        // If no template has been set then add the default HTML tags for this slotname.
        if ($this->_template === null) {
            return $this->_add_html($text);
        } else {
            return new View($this->viewDirectory."text/$this->_template", array(
                'text' => $text,
                'chunk' => $this->_chunk
            ));
        }
    }

    protected function _show_default()
    {
        $text = Kohana::message('chunks', $this->_slotname);
        $text || $text = Kohana::message('chunks', 'text');

        $template = ($this->_template === null) ? $this->_slotname : $this->_template;

        if ( ! Kohana::find_file('views', $this->viewDirectory."text/$template")) {
            return "<p>$text</p>";
        } else {
            return new View($this->viewDirectory."text/$template", array(
                'text'    =>    $text,
                'chunk' => $this->_chunk,
            ));
        }
    }

    public function get_paragraphs($offset = 0, $length = null)
    {
        return \Boom\ParagraphIterator::fromText($this->text());
    }

    public function has_content()
    {
        return trim($this->_chunk->text) != null;
    }

    public function text()
    {
        if (Editor::instance()->isEnabled()) {
            $commander = new TextFilter();
            $commander
                ->addFilter(new Filter\UnmungeAssetEmbeds())
                ->addFilter(new Filter\UnmungeInternalLinks());

            $text = $commander->filterText($this->_chunk->text);
        } else {
            $text = $this->_chunk->site_text;
        }

        return $text;
    }
}
