<?php

namespace Boom\Page;

class Keywords extends \ArrayIterator
{
    public function __toString()
    {
        return implode(', ', $this->getArrayCopy());
    }
}
