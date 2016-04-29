<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\URL;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Console\Command;

class ReassignURL extends Command
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var URL
     */
    protected $url;

    public function __construct(URL $url, Page $page)
    {
        $this->url = $url;
        $this->page = $page;
    }

    public function handle()
    {
        $this->url
            ->setPageId($this->page->getId())
            ->setPrimary(false);

        URLFacade::save($this->url);
    }
}
