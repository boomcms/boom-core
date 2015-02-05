<?php

namespace Boom\Finder;

abstract class Filter
{
    abstract public function execute(\ORM $query);

    public function shouldBeApplied()
    {
        return true;
    }
}
