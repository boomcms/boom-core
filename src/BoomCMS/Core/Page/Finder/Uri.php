<?php

namespace BoomCMS\Core\Page\Finder;

class Uri extends \Boom\Finder\Filter
{
    protected $uri;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function execute(\ORM $query)
    {
        return $query
            ->join('page_urls', 'inner')
            ->on('page.id', '=', 'page_urls.page_id')
            ->where('page_urls.location', '=', $this->uri);
    }
}
