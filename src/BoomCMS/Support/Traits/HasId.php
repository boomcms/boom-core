<?php

namespace BoomCMS\Support\Traits;

trait HasId
{
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : 0;
    }

    /**
     * 
     * @return boolean
     */
    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function setId($id)
    {
        if (!$this->getId()) {
            $this->attributes['id'] = $id;
        }

        return $this;
    }
}