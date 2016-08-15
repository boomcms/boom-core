<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Page\Finder\RelationsOut as Filter;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class RelationsOutTest extends AbstractTestCase
{
    public function testShouldBeAppliedIfPageIsGiven()
    {
        $filter = new Filter($this->validPage());

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testQueryBuild()
    {
        $page = $this->validPage();
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('join')
            ->once()
            ->with('pages_relations', 'pages.id', '=', 'pages_relations.related_page_id')
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with('pages_relations.page_id', '=', $page->getId())
            ->andReturnSelf();

        $filter = new Filter($page);

        $this->assertEquals($query, $filter->execute($query));
    }
}
