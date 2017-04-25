<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\Album as AlbumFilter;
use BoomCMS\Database\Models\Album;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class AlbumTest extends AbstractTestCase
{
    public function testShouldBeAppliedIsTrueWithValidAlbumId()
    {
        $this->markTestSkipped();
    }

    public function testShouldBeAppliedIsTrueWithValidAlbum()
    {
        $this->markTestSkipped();
    }

    public function testShouldBeAppliedIsFalseWithInvalidAlbumId()
    {
        $this->markTestSkipped();
    }

    public function testBuildAppliesAlbumScopeAndReturnsSelf()
    {
        $this->markTestSkipped();
    }
}
