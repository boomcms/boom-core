<?php

namespace BoomCMS\Chunk;

use BoomCMS\Support\Facades\Editor;
use Illuminate\Support\Facades\Lang;

class Html extends BaseChunk
{
    protected function editLink()
    {
        $text = Lang::get('boomcms::editor.html.editlink');

        return "<a href='#'>$text</a>";
    }

    protected function show()
    {
        return Editor::isEnabled() ? $this->editLink() : $this->getContent();
    }

    protected function showDefault()
    {
        return "<a href='#'>{$this->getPlaceholderText()}</a>";
    }

    public function hasContent()
    {
        return isset($this->attrs['html']) && trim($this->attrs['html']) != '';
    }

    public function getContent()
    {
        return $this->hasContent() ? $this->attrs['html'] : '';
    }
}
