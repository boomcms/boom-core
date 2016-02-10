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

        $newPage = m::mock(Page::class.'[addVersion]');
        $newPage->shouldReceive('addVersion');

        PageFacade::shouldReceive('create')
            ->once()
            ->with(m::subset([
                Page::ATTR_SITE => $site->getId(),
            ]))
            ->andReturn($newPage);

        $job = new CreatePage(new Person(), $site);
        $job->handle();
    }

    public function testSiteIdAndParentIdAreSet()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        $parent = new Page([
            Page::ATTR_CHILDREN_VISIBLE_IN_NAV     => true,
            Page::ATTR_CHILDREN_VISIBLE_IN_NAV_CMS => true,
            Page::ATTR_CHILD_TEMPLATE              => 1,
        ]);
        $parent->{Page::ATTR_ID} = 1;

        $newPage = m::mock(Page::class.'[addVersion]');
        $newPage->shouldReceive('addVersion');

        PageFacade::shouldReceive('create')
            ->once()
            ->with(m::subset([
                Page::ATTR_SITE   => $site->getId(),
                Page::ATTR_PARENT => $parent->getId(),
            ]))
            ->andReturn($newPage);

        $job = new CreatePage(new Person(), $site, $parent);
        $job->handle();
    }

    public function testNewPageShouldNotHaveVisibleFromSetToBeInvisible()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        $parent = new Page([
            Page::ATTR_CHILDREN_VISIBLE_IN_NAV     => true,
            Page::ATTR_CHILDREN_VISIBLE_IN_NAV_CMS => true,
            Page::ATTR_CHILD_TEMPLATE              => 1,
        ]);
        $parent->{Page::ATTR_ID} = 1;

        $newPage = m::mock(Page::class.'[addVersion]');
        $newPage->shouldReceive('addVersion');

        PageFacade::shouldReceive('create')
            ->once()
            ->with(m::on(function (array $attrs) {
                return !isset($attrs['visible_from']);
            }))
            ->andReturn($newPage);

        $job = new CreatePage(new Person(), $site, $parent);
        $job->handle();
    }
}
