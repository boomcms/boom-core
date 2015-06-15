<?php

namespace BoomCMS\Core\Commands;

use BoomCMS\Core\URL;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class MakeURLPrimary extends Command implements SelfHandling
{
    /**
     *
     * @var URL\Provider
     */
    protected $provider;

    /**
     *
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
        DB::update('page_urls')
            ->set(['is_primary' => false])
            ->where('page_id', '=', $this->url->getPageId())
            ->where('id', '!=', $this->url->getId())
            ->where('is_primary', '=', true);

        $this->url->setIsPrimary(true);
        $this->provider->save($this->url);

        // Update the primary uri for the page in the pages table.
        DB::update('pages')
            ->set(['primary_uri' => $this->url->getLocation()])
            ->where('id', '=', $this->url->getPageId());
    }
}