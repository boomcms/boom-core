<?php

namespace BoomCMS\Page\History;

use BoomCMS\Contracts\Models\PageVersion;

class Diff
{
    /**
     * Compare two versions.
     *
     * @param PageVersion $new
     * @param PageVersion $old
     *
     * @return Diff\BaseChange
     */
    public function compare(PageVersion $new, PageVersion $old)
    {
        if ($new->isContentChange()) {
            return new Diff\ChunkChange($new, $old);
        }

        if ($new->getTemplateId() !== $old->getTemplateId()) {
            return new Diff\TemplateChange($new, $old);
        }

        if (strcmp($new->getTitle(), $old->getTitle()) !== 0) {
            return new Diff\TitleChange($new, $old);
        }
    }
}
