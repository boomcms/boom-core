<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Finder\Filter as BaseFilter;
use DB;

use Illuminate\Database\Eloquent\Builder;

class Tag extends BaseFilter
{
    protected $tags;

    public function __construct($tags = null)
    {
        if (is_array($tags)) {
            $tags = array_unique($tags);

            if (count($tags) === 1) {
                $tags = $tags[0];
            }
        }

        $this->tags = $tags;
    }

    public function execute(Builder $query)
    {
        $op = (is_array($this->tags)) ? 'IN' : '=';

        $query
            ->join('assets_tags', 'assets_tags.asset_id', '=', 'assets.id')
            ->where('assets_tags.tag', $op, $this->tags);

        if (is_array($this->tags)) {
            $query
                ->groupBy("tag")
                ->having(DB::raw('count(distinct tag)'), '>=', count($this->tags));
        }

        return $query;
    }

    public function shouldBeApplied()
    {
        return ! empty($this->tags);
    }
}
