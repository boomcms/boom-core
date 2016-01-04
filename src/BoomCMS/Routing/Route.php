<?php

namespace BoomCMS\Routing;

use BoomCMS\Contracts\Database\Models\Page;

class Route
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
