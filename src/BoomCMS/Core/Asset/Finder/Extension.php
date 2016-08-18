<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class Extension extends BaseFilter
{
    protected $extensions;

    public function __construct($extensions = null)
    {
        $this->extensions = is_array($extensions) ? $extensions : [$extensions];

        foreach ($this->extensions as $i => $extension) {
            if (empty($extension)) {
                unset($this->extensions[$i]);
            }
        }
    }

    public function build(Builder $query)
    {
        return $query->whereIn('version.extension', $this->extensions);
    }

    public function shouldBeApplied()
    {
        return !empty($this->extensions);
    }
}
