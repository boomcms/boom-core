<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\Album as Filter;
use BoomCMS\Database\Models\Album;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class AlbumTest extends AbstractTestCase
{
    public function testShouldBeAppliedIsTrueWithValidAlbumId()
    {
        $albumId = 1;
        $album = new Album();

        AlbumFacade::shouldReceive('find')
            ->once()
            ->with($albumId)
            ->andReturn($album);

        $filter = new Filter($albumId);

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testShouldBeAppliedIsFalseWithInvalidAlbumId()
    {
        $albumId = 1;

        AlbumFacade::shouldReceive('find')
            ->once()
            ->with($albumId)
            ->andReturn(null);

        $filter = new Filter($albumId);

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testShouldBeAppliedIsTrueWithValidAlbum()
    {
        $album = new Album();
        $filter = new Filter($album);

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testBuildAppliesAlbumScopeAndReturnsSelf()
    {
        $album = new Album();
        $query = m::mock(Builder::class);
        $filter = new Filter($album);

        $query->shouldReceive('whereAlbum')
            ->once()
            ->with($album)
            ->andReturnSelf();

        $this->assertEquals($query, $filter->build($query));
    }
}
