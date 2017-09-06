<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\TitleOrDescriptionContains;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class TitleOrDescriptionContainsTest extends AbstractTestCase
{
    public function testShouldBeAppliedWithValue()
    {
        $filter = new TitleOrDescriptionContains('valid');

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testShouldNotBeAppliedWithEmptyParam()
    {
        $invalid = [null, ''];

        foreach ($invalid as $value) {
            $filter = new TitleOrDescriptionContains($value);

            $this->assertFalse($filter->shouldBeApplied());
        }
    }

    public function testBuild()
    {
        $query = m::mock(Builder::class);
        $subQuery = m::mock(Builder::class);
        $text = 'test';

        $query
            ->shouldReceive('where')
            ->once()
            ->with(m::on(function($closure) use($subQuery) {
                $closure($subQuery);

                return true;
            }))
            ->andReturnSelf();

        $subQuery
            ->shouldReceive('where')
            ->once()
            ->with(Asset::ATTR_TITLE, 'like', "%$text%")
            ->andReturnSelf();

        $subQuery
            ->shouldReceive('orWhere')
            ->once()
            ->with(Asset::ATTR_DESCRIPTION, 'like', "%$text%")
            ->andReturnSelf();

        $filter = new TitleOrDescriptionContains($text);

        $this->assertEquals($query, $filter->build($query));
    }
}
