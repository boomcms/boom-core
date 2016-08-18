<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\Extension;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class ExtensionTest extends AbstractTestCase
{
    public function testShouldNotBeAppliedWithEmptyExtension()
    {
        $extension = '';
        $filter = new Extension($extension);

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testBuildWithSingleExtension()
    {
        $extension = 'gif';
        $filter = new Extension($extension);
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('whereIn')
            ->once()
            ->with('version.extension', [$extension])
            ->andReturnSelf();

        $this->assertEquals($query, $filter->build($query));
    }

    public function testBuildWithMultipleExtensions()
    {
        $extensions = ['gif', 'jpeg'];
        $filter = new Extension($extensions);
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('whereIn')
            ->once()
            ->with('version.extension', $extensions)
            ->andReturnSelf();

        $this->assertEquals($query, $filter->build($query));
    }
}
