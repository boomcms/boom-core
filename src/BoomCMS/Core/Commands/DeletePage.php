<?php

namespace BoomCMS\Core\Commands;

use BoomCMS\Core\Page;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class DeletePage extends Command implements SelfHandling
{
    /**
	 *
	 * @var Page\Page
	 */
    protected $page;

    /**
	 *
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
        $this->provider->delete($this->page);
    }
}
