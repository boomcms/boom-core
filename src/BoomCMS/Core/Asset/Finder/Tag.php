<?php

namespace Boom\Asset\Finder\Filter;

use DB;

class Tag extends \Boom\Finder\Filter
{
    protected $_tags;

    public function __construct($tags = null)
    {
        if (is_array($tags)) {
            $tags = array_unique($tags);

            if (count($tags) === 1) {
                $tags = $tags[0];
            }
        }

        $this->_tags = $tags;
    }

    public function execute(\ORM $query)
    {
        $op = (is_array($this->_tags)) ? 'IN' : '=';

        $query
            ->join('assets_tags', 'inner')
            ->on('assets_tags.asset_id', '=', 'asset.id')
            ->where('assets_tags.tag', $op, $this->_tags);

        if (is_array($this->_tags)) {
            $query
                ->group_by("tag")
                ->having(DB::expr('count(distinct tag)'), '>=', count($this->_tags));
        }

        return $query;
    }

    public function shouldBeApplied()
    {
        return ! empty($this->_tags);
    }
}
