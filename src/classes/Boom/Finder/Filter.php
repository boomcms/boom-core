<?php

namespace BoomCMS\Core\Finder;

abstract class Filter
{
    abstract public function execute(\ORM $query);

    public function shouldBeApplied()
    {
        return true;
    }
}
