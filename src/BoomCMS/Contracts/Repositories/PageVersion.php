<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;

interface PageVersion
{
    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function deleteDrafts(PageInterface $page);
}
