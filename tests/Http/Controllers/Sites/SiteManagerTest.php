<?php

namespace BoomCMS\Tests\Http\Controllers\Sites;

use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\Sites\SiteManager as Controller;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\View;
use Mockery as m;

class SiteManagerTest extends AbstractTestCase
{
    public function testIndex()
    {
        $sites = [new Site(), new Site()];
        $view = 'some view contents';

        SiteFacade::shouldReceive('findAll')
            ->once()
            ->andReturn($sites);

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::sites.index', ['sites' => $sites], [])
            ->andReturn($view);

        $controller = m::mock(Controller::class)->makePartial();

        $this->assertEquals($view, $controller->index());
    }
}
