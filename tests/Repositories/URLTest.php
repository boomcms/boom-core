<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\URL;
use BoomCMS\Repositories\URL as URLRepository;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Support\Helpers\URL as URLHelper;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class URLTest extends AbstractTestCase
{
    /**
     * @var URL
     */
    protected $model;

    /**
     * @var URLRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(URL::class);
        $this->repository = new URLRepository($this->model, $this->site);
    }

    public function testCreateWithUniqueUrl()
    {
        $url = new URL();
        $location = '/test';
        $isPrimary = false;

        $page = m::mock(Page::class)->makePartial();
        $page->shouldReceive('getId')->andReturn(2);

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with([
                URL::ATTR_LOCATION   => 'test',
                URL::ATTR_PAGE_ID    => $page->getId(),
                URL::ATTR_IS_PRIMARY => $isPrimary,
            ])
            ->andReturn($url);

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->andReturn(true);

        $this->assertEquals($url, $this->repository->create($location, $page, $isPrimary));
    }

    public function testCreateWithNonUniqueUrl()
    {
        $url = new URL();
        $location = '/test';
        $isPrimary = false;

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with('test')
            ->andReturn(false);

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with('test1')
            ->andReturn(true);

        $page = m::mock(Page::class)->makePartial();
        $page->shouldReceive('getId')->andReturn(2);

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with([
                URL::ATTR_LOCATION   => 'test1',
                URL::ATTR_PAGE_ID    => $page->getId(),
                URL::ATTR_IS_PRIMARY => $isPrimary,
            ])
            ->andReturn($url);

        $this->assertEquals($url, $this->repository->create($location, $page, $isPrimary));
    }

    public function testDelete()
    {
        $model = m::mock(URL::class);
        $model->shouldReceive('delete')->once();

        $this->assertEquals($this->repository, $this->repository->delete($model));
    }

    public function testFind()
    {
        $url = new URL();
        $url->{URL::ATTR_ID} = 1;

        $this->model
            ->shouldReceive('find')
            ->once()
            ->with($url->getId())
            ->andReturn($url);

        $this->assertEquals($url, $this->repository->find($url->getId()));
    }

    public function testFindByLocation()
    {
        $url = new URL();
        $location = '/test';

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(URL::ATTR_SITE, '=', $this->site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(URL::ATTR_LOCATION, '=', URLHelper::sanitise($location))
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($url);

        $this->assertEquals($url, $this->repository->findByLocation($location));
    }

    public function testIsAvailable()
    {
        $location = 'test';

        foreach ([true, false] as $exists) {
            $this->model
                ->shouldReceive('where')
                ->once()
                ->with(URL::ATTR_SITE, '=', $this->site->getId())
                ->andReturnSelf();

            $this->model
                ->shouldReceive('where')
                ->once()
                ->with(URL::ATTR_LOCATION, '=', $location)
                ->andReturnSelf();

            $this->model
                ->shouldReceive('exists')
                ->once()
                ->andReturn($exists);

            $available = !$exists;
            $this->assertEquals($available, $this->repository->isAvailable($location));
        }
    }

    public function testPage()
    {
        $url = new URL();
        $page = new Page();
        $page->{Page::ATTR_ID} = 1;

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(URL::ATTR_PAGE_ID, '=', $page->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(URL::ATTR_IS_PRIMARY, '=', true)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($url);

        $this->assertEquals($url, $this->repository->page($page));
    }
}
