<?php

namespace BoomCMS\Core\Commands;

use BoomCMS\Core\URL;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\DB;

class MakeURLPrimary extends Command implements SelfHandling
{
    /**
     * @var URL\Provider
     */
    protected $provider;

    /**
     * @var URL\URL
     */
    protected $url;

    public function __construct(URL\Provider $provider, URL\URL $url)
    {
        $this->provider = $provider;
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
        $this->provider->save($this->url);

        DB::table('pages')
            ->where('id', '=', $this->url->getPageId())
            ->update(['primary_uri' => $this->url->getLocation()]);
    }
}
