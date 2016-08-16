<?php

namespace BoomCMS\Page\History\Diff;

class TitleChange extends BaseChange
{
    protected $iconClass = 'header';

    /**
     * @return array
     */
    public function getNewDescriptionParams()
    {
        return [
            'title' => $this->new->getTitle(),
        ];
    }

    /**
     * @return array
     */
    public function getOldDescriptionParams()
    {
        return [
            'title' => $this->old->getTitle(),
        ];
    }
}
