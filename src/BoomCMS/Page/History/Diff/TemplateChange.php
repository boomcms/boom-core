<?php

namespace BoomCMS\Page\History\Diff;

class TemplateChange extends BaseChange
{
    public function getDescriptionParams()
    {
        return [
            'new' => $this->new->getTemplate()->getName(),
            'old' => $this->old->getTemplate()->getName(),
        ];
    }
}
