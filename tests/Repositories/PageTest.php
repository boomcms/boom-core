<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories\Page as PageRepository;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class PageTest extends AbstractTestCase
{
    /**
     * @var Page
     */
    protected $model;

    /**
     * @var PageRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(Page::class);
        $this->repository = m::mock(PageRepository::class, [$this->model])->makePartial();
    }
    
    public function testDelete()
    {
        $page = m::mock(Page::class);
        $page->shouldReceive('delete');

        $this->assertEquals($this->repository, $this->repository->delete($page));
    }

    public function testFindByPrimaryUri()
    {
        $page = new Page();
        $site = new Site();
        $uri = 'test';

        Router::shouldReceive('getActiveSite')->once()->andReturn($site);

        $this->repository
            ->shouldReceive('findBySiteAndPrimaryUri')
            ->once()
            ->with($site, $uri)
            ->andReturn($page);

        $this->assertEquals($page, $this->repository->findByPrimaryUri($uri));
    }

    public function testFindBySiteAndPrimaryUri()
    {
        $page = new Page();
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $uri = 'test';

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_SITE, '=', $site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_PRIMARY_URI, '=', $uri)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($page);

        $this->assertEquals($page, $this->repository->findBySiteAndPrimaryUri($site, $uri));
    }
}
