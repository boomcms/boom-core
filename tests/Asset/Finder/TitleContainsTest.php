<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\TitleContains;
use BoomCMS\Tests\AbstractTestCase;

class TitleContainsTest extends AbstractTestCase
{
    public function testShouldBeAppliedWithValue()
    {
        $filter = new TitleContains('valid');
        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testShouldNotBeAppliedWithEmptyParam()
    {
        $invalid = [null, ''];

        foreach ($invalid as $value) {
            $filter = new TitleContains($value);
            $this->assertFalse($filter->shouldBeApplied());
        }
    }
}
