<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Database\Models\Site;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Support\Helpers\URL;
use BoomCMS\Tests\AbstractTestCase;

class URLTest extends AbstractTestCase
{
    public function testMakeUniqueWithUniqueUrl()
    {
        $site = new Site();
        $location = 'test';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $location)
            ->andReturn(true);

        $this->assertEquals($location, URL::makeUnique($site, $location));
    }

    public function testMakeUniqueAppendsIncrementalNumber()
    {
        $site = new Site();
        $location = 'test';
        $unique = 'test1';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $location)
            ->andReturn(false);

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $unique)
            ->andReturn(true);

        $this->assertEquals($unique, URL::makeUnique($site, $location));
    }
}
