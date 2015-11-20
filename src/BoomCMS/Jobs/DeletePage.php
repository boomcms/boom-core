<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Events\PageWasDeleted;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;

class DeletePage extends Command implements SelfHandling
{
    /**
     * @var Page
     */
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function handle()
    {
        PageFacade::delete($this->page);
        Event::fire(new PageWasDeleted($this->page));
    }
}
