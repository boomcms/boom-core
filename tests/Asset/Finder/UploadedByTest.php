<?php

namespace BoomCMS\Tests\Asset\Finder;

use BoomCMS\Core\Asset\Finder\UploadedBy;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Person;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class UploadedByTest extends AbstractTestCase
{
    public function testShouldNotBeApplied()
    {
        $filter = new UploadedBy([]);

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testBuildWithPersonGiven()
    {
        $person = new Person();
        $person->{Person::ATTR_ID} = 1;

        $filter = new UploadedBy($person);

        $this->assertTrue($filter->shouldBeApplied());

        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Asset::ATTR_UPLOADED_BY, $person->getId())
            ->andReturnSelf();

        $this->assertEquals($query, $filter->build($query));
    }

    public function testBuildWithPersonIdGiven()
    {
        $person = new Person();
        $person->{Person::ATTR_ID} = 1;

        PersonFacade::shouldReceive('find')
            ->once()
            ->with($person->getId())
            ->andReturn($person);

        $filter = new UploadedBy($person->getId());

        $this->assertTrue($filter->shouldBeApplied());

        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Asset::ATTR_UPLOADED_BY, $person->getId())
            ->andReturnSelf();

        $this->assertEquals($query, $filter->build($query));
    }
}
