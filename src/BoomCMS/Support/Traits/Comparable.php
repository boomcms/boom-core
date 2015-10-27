<?php

namespace BoomCMS\Support\Traits;

trait Comparable
{
    public function is($other)
    {
        return is_object($other)
            && (get_class($this) === get_class($other))
            && ($this->getId() > 0)
            && ($this->getId() === $other->getId());
    }
}
