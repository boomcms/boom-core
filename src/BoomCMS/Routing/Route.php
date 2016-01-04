<?php

namespace BoomCMS\Routing;

use BoomCMS\Contracts\Database\Models\Pages;

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
