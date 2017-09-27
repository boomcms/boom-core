<?php

namespace BoomCMS\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use BoomCMS\Support\Helpers\Asset;
use Illuminate\Database\Eloquent\Builder;

class Type extends BaseFilter
{
    protected $type;

    public function __construct($types = null)
    {
        $types = is_array($types) ? $types : [$types];
        $this->type = $this->removeInvalidTypes($types);
    }

    public function build(Builder $query)
    {
        return $query->whereIn('type', $this->type);
    }

    public function removeInvalidTypes(array $types)
    {
        $validTypes = array_keys(Asset::types());

        return array_intersect($validTypes, $types);
    }

    public function shouldBeApplied()
    {
        return !empty($this->type);
    }
}
