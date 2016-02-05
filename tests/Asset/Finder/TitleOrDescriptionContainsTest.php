<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\TitleOrDescriptionContains;
use BoomCMS\Tests\AbstractTestCase;

class TitleOrDescriptionContainsTest extends AbstractTestCase
{
    public function testShouldBeAppliedWithValue()
    {
        $filter = new TitleOrDescriptionContains('valid');
        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testShouldNotBeAppliedWithEmptyParam()
    {
        $invalid = [null, ''];

        foreach ($invalid as $value) {
            $filter = new TitleOrDescriptionContains($value);
            $this->assertFalse($filter->shouldBeApplied());
        }
    }
}
