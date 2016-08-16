<?php

namespace BoomCMS\Page\History\Diff;

class TemplateChange extends BaseChange
{
    protected $iconClass = 'file-o';

    /**
     * @return array
     */
    public function getNewDescriptionParams()
    {
        return [
            'template' => $this->new->getTemplate()->getName(),
        ];
    }

    /**
     * @return array
     */
    public function getOldDescriptionParams()
    {
        return [
            'template' => $this->old->getTemplate()->getName(),
        ];
    }
}
