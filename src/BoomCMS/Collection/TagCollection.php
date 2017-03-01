<?php

namespace BoomCMS\Collection;

use BoomCMS\Contracts\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagCollection extends Collection
{
    /**
     * Returns an array of tag names.
     *
     * @return array
     */
    public function getNames()
    {
        return $this->map(function (Tag $tag) {
            return $tag->getName();
        })->toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(', ', $this->getNames());
    }
}
