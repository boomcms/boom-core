<?php

use BoomCMS\Core\Asset\Collection;

class Asset_CollectionTest extends TestCase
{
    public function testEmptyAssetIdsIgnored()
    {
        $collection = new Collection(['', 1, 2, 3]);
        $this->assertEquals([1, 2, 3], $collection->getAssetIds());
    }
}
