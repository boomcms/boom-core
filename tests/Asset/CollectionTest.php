<?php

namespace BoomCMS\Tests\Asset;

use BoomCMS\Core\Asset\Collection;
use BoomCMS\Tests\AbstractTestCase;

class CollectionTest extends AbstractTestCase
{
    public function testEmptyAssetIdsIgnored()
    {
        $collection = new Collection(['', 1, 2, 3]);
        $this->assertEquals([1, 2, 3], $collection->getAssetIds());
    }
}
