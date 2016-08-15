<?php

namespace BoomCMS\Page\History\Diff;

class TitleChange extends BaseChange
{
    public function getDescriptionParams()
    {
        return [
            'new' => $this->new->getTitle(),
            'old' => $this->old->getTitle(),
        ];
    }
}
