<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Support\Helpers\URL as URLHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class CreatePagePrimaryUri extends Command
{
    protected $location;
    protected $page;
    protected $prefix;

    public function __construct(Page $page, $prefix = null, $location = null)
    {
        $this->location = $location;
        $this->page = $page;
        $this->prefix = $prefix;
    }

    public function handle()
    {
        $url = ($this->location !== null) ?
            $this->location
            : URLHelper::fromTitle($this->prefix, $this->page->getTitle());

        $url = URLFacade::create($url, $this->page, true);
        Bus::dispatch(new MakeURLPrimary($url));

        return $url;
    }
}
