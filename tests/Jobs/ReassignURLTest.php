<?php

namespace BoomCMS\Tests\Jobs;

use BoomCMS\Database\Models\URL;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class ReassignURLTest extends AbstractTestCase
{
    public function testHandle()
    {
        $page = $this->validPage();

        $url = m::mock(URL::class);

        $url
            ->shouldReceive('setPageId')
            ->with($page->getId())
            ->andReturnSelf();

        $url
            ->shouldReceive('setIsPrimary')
            ->with(false)
            ->andReturnSelf();

        URLFacade::shouldReceive('save')->with($url);
    }
}
