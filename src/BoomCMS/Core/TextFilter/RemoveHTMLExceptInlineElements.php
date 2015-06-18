<?php

namespace BoomCMS\Core\TextFilter;

class RemoveHTMLExceptInlineElements implements Filter
{
    public function filterText($text)
    {
        return \strip_tags($text, '<b><i><a>');
    }
}
