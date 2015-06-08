<?php

use BoomCMS\Core\Page;
use Illuminate\Console\Command;

class AddURLToPage extends Command
{
    protected $page;
    protected $url;
    protected $isPrimary;

    public function __construct(Page\Page $page, $url, $isPrimary = false)
    {
        $this->page = $page;
        $this->url = $url;
        $this->isPrimary = $isPrimary;
    }

    public function handle()
    {
        
    }
}