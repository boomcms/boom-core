<?php

namespace BoomCMS\Core\Commands;

use BoomCMS\Core\Page;
use BoomCMS\Core\URL;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class CreatePagePrimaryUri extends Command implements SelfHandling
{

    protected $location;
    protected $page;
    protected $prefix;
    protected $provider;

    public function __construct(Page\Provider $provider, Page\Page $page, $prefix = null, $location = null)
    {
        $this->location = $location;
        $this->page = $page;
        $this->prefix = $prefix;
        $this->provider = $provider;
    }

    public function handle()
    {
        $url = $this->location ?: URL\Helpers::fromTitle($this->prefix, $this->page->getTitle());

        $this->page->setPrimaryUri($url);
        $page = $this->provider->save($this->page);

        return URL\URL::create($url, $page->getId(), true);
    }
}