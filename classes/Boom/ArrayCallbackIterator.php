<?php

namespace Boom;

class ArrayCallbackIterator extends \ArrayIterator
{
    private $callback;

    public function __construct($value, $callback)
    {
        parent::__construct($value);
        $this->callback = $callback;
    }

    public function current()
    {
        $value = parent::current();

        return call_user_func($this->callback, $value);
    }

    public function offsetGet($index)
    {
        $value = parent::offsetGet($index);

        return call_user_func($this->callback, $value);
    }
}
