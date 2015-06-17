<?php

namespace BoomCMS\Core\TextFilter;

class RemoveAllHTML implements Filter
{
    public function filterText($text)
    {
        return \strip_tags($text);
    }
}
