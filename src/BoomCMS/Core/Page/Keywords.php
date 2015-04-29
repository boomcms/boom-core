<?php

namespace BoomCMS\Core\Page;

class Keywords extends \ArrayIterator
{
    public function __toString()
    {
        return implode(', ', $this->getArrayCopy());
    }
}
