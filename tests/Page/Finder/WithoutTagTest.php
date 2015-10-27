<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Core\Page\Finder\WithoutTag as Filter;
use BoomCMS\Core\Tag\Tag;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class WithoutTagTest extends AbstractTestCase
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
            ->with('pages_tags as pt_without-0', m::on(function () {
                return true;
            }))
            ->andReturnSelf()
            ->shouldReceive('on')
            ->with('pages.id', '=', 'pt_without-0.page_id')
            ->andReturnSelf()
            ->shouldReceive('on')
            ->with('pt_without-0.tag_id', '=', $tag->getId())
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('pt_without-0.page_id');

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
