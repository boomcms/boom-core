<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Core\Page\Finder\Tag;
use BoomCMS\Database\Models\Tag as TagModel;
use BoomCMS\Support\Facades\Tag as TagFacade;
use BoomCMS\Tests\AbstractTestCase;

class TagTest extends AbstractTestCase
{
    public function testAcceptsArrayOfTagIds()
    {
        $args = [
            [1, 2],
            ['1', '2'],
        ];

        foreach ($args as $a) {
            $tag = new TagModel();
            $tag->id = 1;

            TagFacade::shouldReceive('find')->once()->with($a[1])->andReturn($tag);
            TagFacade::shouldReceive('find')->once()->with($a[0])->andReturn($tag);

            $filter = new Tag($a);

            $this->assertTrue($filter->shouldBeApplied());
        }
    }
}
