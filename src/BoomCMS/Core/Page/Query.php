<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Foundation\Query as BaseQuery;

class Query extends BaseQuery
{
    protected $filterAliases = [
        'ignorepages'         => Finder\IgnorePages::class,
        'not'                 => Finder\IgnorePages::class,
        'pageid'              => Finder\PageId::class,
        'parentid'            => Finder\ParentId::class,
        'parent'              => Finder\ParentPage::class,
        'tag'                 => Finder\Tag::class,
        'template'            => Finder\Template::class,
        'uri'                 => Finder\Uri::class,
        'relatedbytags'       => Finder\RelatedByTags::class,
        'visibleinnavigation' => Finder\VisibleInNavigation::class,
        'nextto'              => Finder\NextTo::class,
        'title'               => Finder\Title::class,
        'search'              => Finder\Search::class,
        'relatedto'           => Finder\RelatedTo::class,
        'withouttag'          => Finder\WithoutTag::class,
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

    public function getNextTo(Page $page, $direction)
    {
        $params = $this->params;

        if (isset($params['parent'])) {
            unset($params['parent']);
        }

        $params['parentid'] = $page->getParentId();
        $params['nextto'] = [$page, $direction];
        $finder = $this->addFilters(new Finder\Finder(), $params);

        return $finder->find();
    }
}
