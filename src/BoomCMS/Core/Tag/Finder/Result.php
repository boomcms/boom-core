<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Core\Tag\Tag as Tag;

class Result extends \ArrayIterator
{
    public function __construct(\Database_Result $results)
    {
        $results = $results->as_array();

        foreach ($results as &$result) {
            $result = new Tag($result);
        }

        parent::__construct($results);
    }
}
