<?php

namespace BoomCMS\Tests\Tag;

use BoomCMS\Core\Tag\Finder\AppliedToPage;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class AppliedToPageTest extends AbstractTestCase
{
    public function testExcecuteBuildsQuery()
    {
        $filter = new AppliedToPage($this->validPage());
        $query = m::mock(Builder::class);

        $query->shouldReceive('join')
            ->once()
            ->with('pages_tags', 'tags.id', '=', 'pages_tags.tag_id')
            ->andReturnSelf();

        $query->shouldReceive('where')
            ->once()
            ->with('pages_tags.page_id', 1)
            ->andReturnSelf();

        $filter->execute($query);
    }
}
