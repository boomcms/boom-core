<?php

namespace BoomCMS\Commands;

use BoomCMS\Core\Page;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Bus;

class DeletePageChildren extends Command implements SelfHandling
{
    /**
     * @var Page\Page
     */
    protected $page;

    /**
     * @var Page\Provider
     */
    protected $provider;

    public function __construct(Page\Provider $provider, Page\Page $page)
    {
        $this->page = $page;
        $this->provider = $provider;
    }

    public function handle()
    {
        $this->doDelete($this->page);
    }

    protected function doDelete(Page\Page $page)
    {
        $children = $this->provider->findByParentId($page->getId());

        foreach ($children as $child) {
            $this->doDelete($child);
            Bus::dispatch(new DeletePage($this->provider, $child));
        }
    }
}
