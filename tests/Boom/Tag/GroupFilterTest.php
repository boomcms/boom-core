<?php

use BoomCMS\Core\Tag;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class Tag_GroupFilterTest extends TestCase
{
    public function testGroupIsEmptySearchesForUngroupedTags()
    {
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('whereNull')
            ->once()
            ->with('group');

        $filter = new Tag\Finder\Group('');
        $filter->execute($query);
    }

    public function testGroupIsntEmptySearchesForTagsInGivenGroup()
    {
        $group = 'test';
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->once()
            ->with('group', '=', $group);

        $filter = new Tag\Finder\Group($group);
        $filter->execute($query);
    }
}
