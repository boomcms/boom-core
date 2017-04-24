<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Foundation\Query as BaseQuery;

class Query extends BaseQuery
{
    protected $filterAliases = [
        'album'                      => Finder\Album::class,
        'title'                      => Finder\TitleContains::class,
        'titleordescriptioncontains' => Finder\TitleOrDescriptionContains::class,
        'type'                       => Finder\Type::class,
        'extension'                  => Finder\Extension::class,
        'uploadedby'                 => Finder\UploadedBy::class,
        'uploaded-by'                => Finder\UploadedBy::class,
    ];

    public function count()
    {
        $finder = $this->addFilters(new Finder\Finder(), $this->params);

        return $finder->count();
    }

    public function getResults()
    {
        $finder = $this->addFilters(new Finder\Finder(), $this->params);
        $finder = $this->configurePagination($finder, $this->params);

        return $finder->findAll();
    }
}
