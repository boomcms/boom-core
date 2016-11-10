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
        if ($original = $new->getRestoredVersion()) {
            return new Diff\RestoredVersion($new, $old, $original);
        }

        if ($new->isContentChange()) {
            return new Diff\ChunkChange($new, $old);
        }

        if ($new->getTemplateId() !== $old->getTemplateId()) {
            return new Diff\TemplateChange($new, $old);
        }

        if (strcmp($new->getTitle(), $old->getTitle()) !== 0) {
            return new Diff\TitleChange($new, $old);
        }

        if ($new->isPendingApproval() && !$old->isPendingApproval()) {
            return new Diff\ApprovalRequest($new, $old);
        }

        if ($new->isEmbargoed($new->getEditedTime())) {
            if (!$old->isEmbargoed($old->getEditedTime())) {
                return new Diff\Embargoed($new, $old);
            }

            if ($new->getEmbargoedUntil()->getTimestamp() !== $old->getEmbargoedUntil()->getTimestamp()) {
                return new Diff\EmbargoChanged($new, $old);
            }
        }

        if ($new->isPublished($new->getEditedTime())) {
            if (!$old->isPublished($old->getEditedTime())) {
                return new Diff\Published($new, $old);
            }
        }
    }
}
