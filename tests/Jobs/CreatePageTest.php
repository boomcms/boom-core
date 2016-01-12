<?php

namespace BoomCMS\Tests\Jobs;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Jobs\CreatePage;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class CreatePageTest extends AbstractTestCase
{
    public function testSiteIdOfNewPageIsSet()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        PageFacade::shouldReceive('create')
            ->once()
            ->with(m::contains([
                Page::ATTR_SITE => $site->getId(),
            ]));

        $job = new CreatePage(new Person(), $site);
        $job->handle();
    }
}
