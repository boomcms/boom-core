<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\Type;
use BoomCMS\Tests\Support\Helpers\AssetTest;

class TypeTest extends AssetTest
{
    public function testRemoveInvalidTypes()
    {
        $types = ['invalid', 'image'];
        $filter = new Type([]);

        $this->assertEquals(['image'], $filter->removeInvalidTypes($types));
    }

    public function testShouldNotBeAppliedWithNoValidTypes()
    {
        $filter = new Type(['invalid']);

        $this->assertFalse($filter->shouldBeApplied());
    }
}