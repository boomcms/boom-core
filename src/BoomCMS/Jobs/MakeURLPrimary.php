<?php

namespace BoomCMS\Jobs;

use BoomCMS\Core\URL;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\DB;

class MakeURLPrimary extends Command implements SelfHandling
{
    /**
     * @var URL\URL
     */
    protected $url;

    public function __construct(URL\URL $url)
    {
        $this->url = $url;
    }

    public function handle()
    {
        DB::table('page_urls')
            ->where('page_id', '=', $this->url->getPageId())
            ->where('id', '!=', $this->url->getId())
            ->where('is_primary', '=', true)
            ->update(['is_primary' => false]);

        $this->url->setIsPrimary(true);
        URLFacade::save($this->url);

        DB::table('pages')
            ->where('id', '=', $this->url->getPageId())
            ->update(['primary_uri' => $this->url->getLocation()]);
    }
}
