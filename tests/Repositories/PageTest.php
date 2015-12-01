<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Page;
use BoomCMS\Repositories\Page as PageRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class PageTest extends AbstractTestCase
{
    public function testDelete()
    {
        $page = m::mock(Page::class);
        $page->shouldReceive('delete');

        $repository = new PageRepository(new Page());

        $this->assertEquals($repository, $repository->delete($page));
    }
}
