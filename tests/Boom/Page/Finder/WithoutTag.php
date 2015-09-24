<?php

use BoomCMS\Core\Page\Finder\WithoutTag as Filter;
use BoomCMS\Core\Tag\Tag;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class Page_Finder_WithoutTagTest extends TestCase
{
    public function testShouldBeAppliedIfTagIsValid()
    {
        $tag = new Tag(['id' => 1]);
        $filter = new Filter($tag);

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testShouldNotBeAppliedIfTagIsInvalid()
    {
        $tag = new Tag();
        $filter = new Filter($tag);

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testQueryIsBuilt()
    {
        $query = m::mock(Builder::class);
        $tag = new Tag(['id' => 1]);
        $filter = new Filter($tag);

        $query
            ->shouldReceive('leftJoin')
            ->with('pages_tags as pt_without', 'pages.id', '=', 'pt_without.page_id')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('pt_without.tag_id', '=', $tag->getId())
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('pt_without.page_id');

        $filter->build($query);
    }

    public function testQueryObjectIsReturned()
    {
        $tag = new Tag(['id' => 1]);
        $filter = new Filter($tag);
        $query = m::mock(Builder::class);
        $query->shouldIgnoreMissing($query);

        $this->assertEquals($query, $filter->build($query));
    }
}
