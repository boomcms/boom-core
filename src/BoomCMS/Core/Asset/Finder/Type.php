<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class Type extends BaseFilter
{
    protected $type;

    public function __construct($types = null)
    {
        $types = is_array($types) ?: [$types];
        $this->type = $this->removeInvalidTypes($types);
    }

    public function build(Builder $query)
    {
        return $query->whereIn('type', $this->type);
    }

    private function removeInvalidTypes($types)
    {
        $validTypes = [];

        foreach ($types as $type) {
            if ($type) {
                if (!is_int($type) && !ctype_digit($type)) {
                    $validTypes[] = constant('BoomCMS\Core\Asset\Type::'.strtoupper($type));
                } else {
                    $validTypes[] = $type;
                }
            }
        }

        return $validTypes;
    }

    public function shouldBeApplied()
    {
        return !empty($this->type);
    }
}
