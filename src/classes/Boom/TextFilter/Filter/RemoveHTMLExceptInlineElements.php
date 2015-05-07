<?php

namespace BoomCMS\Core\TextFilter\Filter;

class RemoveHTMLExceptInlineElements implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        return \strip_tags($text, '<b><i><a>');
    }
}
