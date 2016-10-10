<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Database\Models\Tag as TagModel;
use BoomCMS\Page\Finder\AllTags;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Mockery as m;

class AllTagsTest extends AbstractTestCase
{
    public function testBuild()
    {
        $tag1 = new TagModel();
        $tag1->{TagModel::ATTR_ID} = 1;

        $tag2 = new TagModel();
        $tag2->{TagModel::ATTR_ID} = 2;

        $filter = new AllTags([$tag1, $tag2]);

        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('join')
            ->once()
            ->with('pages_tags', 'pages.id', '=', 'pages_tags.page_id')
            ->andReturnSelf();

        $query
            ->shouldReceive('whereIn')
            ->once()
            ->with('pages_tags.tag_id', [1, 2])
            ->andReturnSelf();

        $query
            ->shouldReceive('groupBy')
            ->once()
            ->with('pages_tags.tag_id')
            ->andReturnSelf();

        $query
            ->shouldReceive('having')
            ->once()
            ->with(m::on(function (Expression $expression) {
                return $expression->getValue() === 'count(distinct pages_tags.tag_id)';
            }), '=', 2)
            ->andReturnSelf();

        $this->assertEquals($query, $filter->build($query));
    }
}
