<?php

namespace BoomCMS;

use Illuminate\Contracts\Cache\Repository;

class BoomCMS
{
    /**
     * The BoomCMS version.
     *
     * @var string
     */
    const VERSION = '6.0.0-Dev';

    /**
     * @var Repository
     */
    private $cache;

    /**
     * URL to to retrieve latest news from.
     *
     * @var string
     */
    private $newsUrl = 'https://www.boomcms.net/news.json';

    /**
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the BoomCMS version.
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    public function getNews()
    {
        $key = 'boomcms.news';

        return $this->cache->get($key, function() use($key) {
            $news = json_decode(file_get_contents($this->newsUrl));

            $this->cache->put($key, $news, 3600);
        });
    }
}
