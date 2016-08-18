<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Page\Finder\PageId;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class PageIdFilterTest extends AbstractTestCase
{
    public function testSinglePageId()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $filter = new PageId(1);

        $this->assertTrue($filter->shouldBeApplied());

        $query
            ->shouldReceive('where')
            ->with('pages.id', '=', 1);

        $filter->build($query);
    }

    public function testMultiplePageIds()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $filter = new PageId([1, 2]);

        $this->assertTrue($filter->shouldBeApplied());

        $query
            ->shouldReceive('where')
            ->with('pages.id', 'in', [1, 2]);

        $filter->build($query);
    }

    public function testNoPageIdShouldNotBeApplied()
    {
        $filter = new PageId(null);

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testNonNumericPageIdShouldNotBeApplied()
    {
        $filter = new PageId('heya');

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testEmptyArrayOfPageIdsShouldNotBeApplied()
    {
        $filter = new PageId([]);

        $this->assertFalse($filter->shouldBeApplied());
    }
}
