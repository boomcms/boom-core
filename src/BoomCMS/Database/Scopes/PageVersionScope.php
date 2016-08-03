<?php

namespace BoomCMS\Database\Scopes;

use BoomCMS\Support\Facades\Editor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class PageVersionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder  $builder
     * @param  Model  $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $subquery = $this->getCurrentVersionQuery();

        $builder
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('pages.*')
            ->join(DB::raw('('.$subquery->toSql().') as v2'), 'pages.id', '=', 'v2.page_id')
            ->mergeBindings($subquery)
            ->join('page_versions as version', function ($join) {
                $join
                    ->on('pages.id', '=', 'version.page_id')
                    ->on('v2.id', '=', 'version.id');
            });
    }

    public function getCurrentVersionQuery()
    {
        $query = DB::table('page_versions')
            ->select([DB::raw('max(id) as id'), 'page_id'])
            ->groupBy('page_id');

        if (Editor::isDisabled()) {
            $query->where('embargoed_until', '<=', time());
        }

        return $query;
    }
}
