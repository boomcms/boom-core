<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\PageVersion as VersionModel;
use BoomCMS\Repositories\PageVersion as VersionRepository;
use BoomCMS\Tests\AbstractTestCase;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class PageVersionTest extends AbstractTestCase
{
    /**
     * @var VersionModel
     */
    protected $model;

    /**
     * @var VersionRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(VersionModel::class);
        $this->repository = new VersionRepository($this->model);
    }

    public function testHistory()
    {
        $page = $this->validPage();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(VersionModel::ATTR_PAGE, $page->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with(VersionModel::ATTR_CREATED_AT, 'desc')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('with')
            ->once()
            ->with('editedBy')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn([]);

        $this->assertEquals([], $this->repository->history($page));
    }
}
