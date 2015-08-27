<?php

use BoomCMS\Core\Page\Finder\PageId;
use Mockery as m;

class Page_Finder_PageIdFilterTest extends TestCase
{
    public function testSinglePageId()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $filter = new PageId(1);

        $this->assertTrue($filter->shouldBeApplied());

        $query
            ->shouldReceive('where')
            ->with('pages.id', '=', 1);
    }

    public function testMultiplePageIds()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $filter = new PageId([1, 2]);

        $this->assertTrue($filter->shouldBeApplied());

        $query
            ->shouldReceive('where')
            ->with('pages.id', 'in', [1, 2]);
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
