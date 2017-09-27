<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Asset\Finder\Type;
use BoomCMS\Tests\AbstractTestCase;

class TypeTest extends AbstractTestCase
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
