<?php

namespace Boom\Asset\Finder;

use Boom\Asset as Asset;

class Result extends \ArrayIterator
{
    public function __construct(\Database_Result $results)
    {
        $results = $results->as_array();

        foreach ($results as &$result) {
            $result = Asset\Factory::fromModel($result);
        }

        parent::__construct($results);
    }
}
