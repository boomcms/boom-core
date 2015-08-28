<?php

namespace BoomCMS\Support\Traits;

trait Renderable
{
    public function __toString()
    {
        return (string) $this->render();
    }

    abstract public function render();
}