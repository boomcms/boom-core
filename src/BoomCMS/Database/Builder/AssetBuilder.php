<?php

namespace BoomCMS\Database\Builder;

use BoomCMS\Foundation\Database\Builder;

class AssetBuilder extends Builder
{
    protected $filterAliases = [
        'tag'                        => Finder\Tag::class,
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
