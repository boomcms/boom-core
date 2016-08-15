<?php

namespace BoomCMS\Page\History;

use BoomCMS\Contracts\Models\PageVersion;

class Diff
{
    /**
     * Compare two versions
     *
     * @param PageVersion $new
     * @param PageVersion $old
     *
     * @return Diff\BaseChange
     */
    public function compare(PageVersion $new, PageVersion $old)
    {
        if ($new->getTemplateId() !== $old->getTemplateId()) {
            $className = Diff\TemplateChange::class;
        }

        if (strcmp($new->getTitle(), $old->getTitle()) !== 0) {
            $className = Diff\TitleChange::class;
        }

        if (isset($className)) {
            return new $className($new, $old);
        }
    }
}
