<?php

use BoomCMS\Core\Page\Finder\IgnorePages as Filter;
use Mockery as m;

class Page_Finder_IgnorePagesFilterTest extends TestCase
{
    public function testSinglePageId()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $filter = new Filter(1);

        $this->assertTrue($filter->shouldBeApplied());

        $query
            ->shouldReceive('where')
            ->with('pages.id', '!=', 1);

        $filter->build($query);
    }

    public function testMultiplePageIds()
    {
        $query = m::mock('Illuminate\Database\Eloquent\Builder');
        $filter = new Filter([1, 2]);

        $this->assertTrue($filter->shouldBeApplied());

        $query
            ->shouldReceive('where')
            ->with('pages.id', 'not in', [1, 2]);

        $filter->build($query);
    }

    public function testNoPageIdShouldNotBeApplied()
    {
        $filter = new Filter(null);

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testNonNumericPageIdShouldNotBeApplied()
    {
        $filter = new Filter('heya');

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testEmptyArrayOfPageIdsShouldNotBeApplied()
    {
        $filter = new Filter([]);

        $this->assertFalse($filter->shouldBeApplied());
    }
}
