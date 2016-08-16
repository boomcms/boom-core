<?php

namespace BoomCMS\Page\History\Diff;

class TitleChange extends BaseChange
{
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
