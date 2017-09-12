<?php

namespace BoomCMS\Tests\Jobs;

use BoomCMS\Database\Models\Page as PageModel;
use BoomCMS\Jobs;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Event;
use Mockery as m;

class DeletePageJobTest extends AbstractTestCase
{
    public function testChildrenAreDeletedIfNotReparented()
    {
        $options = [
            ['reparentChildrenTo' => null],
            ['reparentChildrenTo' => 0],
            [],
        ];

        foreach ($options as $o) {
            $page = $this->validPage();
            $job = new Jobs\DeletePage($page, $o);

            Page::shouldReceive('findByParentId')
                ->once()
                ->with($page->getId())
                ->andReturn(null);

            Page::shouldReceive('delete')
                ->once()
                ->with($page);

            Page::shouldDeferMissing();
            Event::shouldReceive('fire')->once();

            $job->handle();
        }
    }

    public function testChildrenAreReparented()
    {
        $page = $this->validPage();
        $newParent = $this->validPage(2);

        $child = m::mock(PageModel::class);
        $child
            ->shouldReceive('setParent')
            ->once()
            ->with($newParent)
            ->andReturnSelf();

        $job = new Jobs\DeletePage($page, ['reparentChildrenTo' => $newParent->getId()]);

        Page::shouldReceive('delete')->zeroOrMoreTimes();
        Event::shouldReceive('fire')->zeroOrMoreTimes();

        Page::shouldReceive('find')->with($newParent->getId())->andReturn($newParent);
        Page::shouldReceive('findByParentId')->with($page->getId())->andReturn([$child]);
        Page::shouldReceive('save')->with($child);

        $job->handle();
    }

    public function testUrlsAreMovedWhenDeletingChildren()
    {
        $page = m::mock(PageModel::class)->makePartial();
        $redirectTo = $this->validPage(2);
        $options = ['redirectTo' => 2];

        $page
            ->shouldReceive('getUrls')
            ->once()
            ->andReturn([]);

        $job = new Jobs\DeletePage($page, $options);

        Page::shouldReceive('recurse')->once();
        Page::shouldReceive('find')->with($redirectTo->getId())->andReturn($redirectTo);

        Event::shouldReceive('fire')->zeroOrMoreTimes();

        $job->handle();
    }
}
