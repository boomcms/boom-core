<?php

namespace BoomCMS\Page\History\Diff;

use BoomCMS\Contracts\Models\PageVersion;

class RestoredVersion extends BaseChange
{
    protected $iconClass = 'backward';

    /**
     * @var PageVersion
     */
    protected $original;

    /**
     * @param PageVersion $new
     * @param PageVersion $old
     * @param PageVersion $original
     */
    public function __construct(PageVersion $new, PageVersion $old, PageVersion $original)
    {
        parent::__construct($new, $old);

        $this->original = $original;
    }
}
