<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Core\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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

    public function build(Builder $query)
    {
        $query->join('assets_tags', 'assets_tags.asset_id', '=', 'assets.id');

        if (is_array($this->tags)) {
            $query
                ->whereIn('assets_tags.tag', $this->tags)
                ->groupBy('tag')
                ->having(DB::raw('count(distinct tag)'), '=', count($this->tags));
        } else {
            $query->where('assets_tags.tag', '=', $this->tags);
        }

        return $query;
    }

    public function shouldBeApplied()
    {
        return !empty($this->tags);
    }
}
