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

    public function testDeleteDrafts()
    {
        $pageLastPublished = time();
        $query = m::mock(Builder::class);

        $page = m::mock(Page::class)->makePartial();
        $page->{Page::ATTR_ID} = 1;

        $page->shouldReceive('getLastPublishedTime')
            ->once()
            ->andReturn(new DateTime('@'.$pageLastPublished));

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(VersionModel::ATTR_PAGE, $page->getId())
            ->andReturnSelf();

        $query
            ->shouldReceive('whereNull')
            ->once()
            ->with(VersionModel::ATTR_EMBARGOED_UNTIL)
            ->andReturnSelf();

        $query
            ->shouldReceive('orWhere')
            ->once()
            ->with(VersionModel::ATTR_EMBARGOED_UNTIL, '>', time())
            ->andReturnSelf();

        $this->model->shouldReceive('where')
            ->once()
            ->andReturnUsing(function ($callback) use ($query) {
                $callback($query);

                return $this->model;
            });

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(VersionModel::ATTR_EDITED_AT, '>', $pageLastPublished)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('delete')
            ->once();

        $this->repository->deleteDrafts($page);
    }
}
