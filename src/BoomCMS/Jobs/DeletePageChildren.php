<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Bus;

class DeletePageChildren extends Command implements SelfHandling
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
        $this->doDelete($this->page);
    }

    protected function doDelete(Page $page)
    {
        $children = PageFacade::findByParentId($page->getId());

        foreach ($children as $child) {
            $this->doDelete($child);
            Bus::dispatch(new DeletePage($child));
        }
    }
}
