<?php

namespace BoomCMS\Support\Traits;

trait HasId
{
    public function getId()
    {
        return $this->id ? (int) $this->id : 0;
    }

    /**
     * @return bool
     */
    public function loaded()
    {
        return $this->getId() > 0;
    }
}
