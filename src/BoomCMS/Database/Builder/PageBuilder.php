<?php

namespace BoomCMS\Database\Builder;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Foundation\Finder\Finder as BaseFinder;
use BoomCMS\Foundation\Database\Builder;
use BoomCMS\Support\Facades\Router;
use Illuminate\Support\Facades\Auth;

class PageBuilder extends Builder
{
    const TITLE = 'version.title';
    const MANUAL = 'sequence';
    const DATE = 'visible_from';
    const EDITED = 'version.created_at';

    protected $filterAliases = [
        'acl'                 => Finder\Acl::class,
        'alltags'             => Finder\AllTags::class,
        'excludeinvisible'    => Finder\ExcludeInvisible::class,
        'ignorepages'         => Finder\IgnorePages::class,
        'not'                 => Finder\IgnorePages::class,
        'pageid'              => Finder\PageId::class,
        'parentid'            => Finder\ParentId::class,
        'parent'              => Finder\ParentPage::class,
        'pendingapproval'     => Finder\PendingApproval::class,
        'tag'                 => Finder\Tag::class,
        'template'            => Finder\Template::class,
        'uri'                 => Finder\Uri::class,
        'relatedbytags'       => Finder\RelatedByTags::class,
        'visibleinnavigation' => Finder\VisibleInNavigation::class,
        'nextto'              => Finder\NextTo::class,
        'title'               => Finder\Title::class,
        'search'              => Finder\Search::class,
        'relatedto'           => Finder\RelationsOut::class,
        'relationsout'        => Finder\RelationsOut::class,
        'relationsin'         => Finder\RelationsIn::class,
        'withouttag'          => Finder\WithoutTag::class,
        'year'                => Finder\Year::class,
        'yearandmonth'        => Finder\YearAndMonth::class,
    ];

    public function __construct(PageInterface $model, array $params)
    {
        // Exclude invisible should be included by default
        // To prevent invisible pages showing up in the site to non CMS users.
        if (!isset($params['excludeinvisible'])) {
            $params['excludeinvisible'] = null;
        }

        // Always include the ACL filter.
        $params['acl'] = [Router::getActiveSite(), Auth::user()];

        $this->model = $model;
        $this->params = $params;
    }

    public function configurePagination(BaseFinder $finder, array $params)
    {
        if (isset($params['order'])) {
            list($column, $direction) = explode(' ', strtoupper($params['order']));

            if ($column && $direction) {
                $column = constant(static::class.'::'.$column);
                $direction = constant(static::class.'::'.$direction);

                $params['order'] = "$column $direction";
            }
        }

        return parent::configurePagination($finder, $params);
    }

    public function getNextTo(PageInterface $page, $direction)
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
