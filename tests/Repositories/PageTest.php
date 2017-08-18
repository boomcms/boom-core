<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Page;
use BoomCMS\Repositories\Page as PageRepository;
use Mockery as m;

class PageTest extends BaseRepositoryTest
{
    protected $modelClass = Page::class;

    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        parent::setUp();

        $this->repository = m::mock(PageRepository::class, [$this->model, $this->site])->makePartial();
        $this->page = m::mock(Page::class);
    }

    public function testFindReturnsModelById()
    {
        $this->withCurrentVersion();

        parent::testFindReturnsModelById();
    }

    public function testFindReturnsNull()
    {
        $this->withCurrentVersion();

        parent::testFindReturnsNull();
    }

    public function testFindByPrimaryUri()
    {
        $uri = 'test';

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_SITE, '=', $this->site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_PRIMARY_URI, '=', $uri)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($this->page);

        $this->assertEquals($this->page, $this->repository->findByPrimaryUri($uri));
    }

    public function testFindByPrimaryUriWithArray()
    {
        $uris = ['test1', 'test2'];

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_SITE, '=', $this->site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_PRIMARY_URI, 'in', $uris)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn([$this->page, $this->page]);

        $this->assertEquals([$this->page, $this->page], $this->repository->findByPrimaryUri($uris));
    }

    public function testFindByUri()
    {
        $uri = 'test';

        $this->model
            ->shouldReceive('join')
            ->once()
            ->with('page_urls', 'page_urls.page_id', '=', 'pages.id')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with('pages.site_id', '=', $this->site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with('location', '=', $uri)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('select')
            ->once()
            ->with('pages.*')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($this->page);

        $this->assertEquals($this->page, $this->repository->findByUri($uri));
    }

    public function testFindByUriWithArray()
    {
        $uris = ['test1', 'test2'];

        $this->model
            ->shouldReceive('join')
            ->once()
            ->with('page_urls', 'page_urls.page_id', '=', 'pages.id')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with('pages.site_id', '=', $this->site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with('location', 'in', $uris)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('select')
            ->once()
            ->with('pages.*')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn([$this->page, $this->page]);

        $this->assertEquals([$this->page, $this->page], $this->repository->findByUri($uris));
    }

    public function testInternalNameExists()
    {
        $name = 'test';
        $exists = true;

        $this->model
            ->shouldReceive('withTrashed')
            ->once()
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with('internal_name', $name)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('exists')
            ->once()
            ->andReturn($exists);

        $this->assertEquals($exists, $this->repository->internalNameExists($name));
    }

    public function testRecurse()
    {
        $pageId = 1;
        $children = [m::mock(Page::class), m::mock(Page::class)];

        $this->repository
            ->shouldReceive('findByParentId')
            ->once()
            ->with($pageId)
            ->andReturn($children);

        $this->model
            ->shouldReceive('getId')
            ->once()
            ->andReturn($pageId);

        $this->model
            ->shouldReceive('save')
            ->once();

        foreach ($children as $i => $child) {
            $child
                ->shouldReceive('getId')
                ->once()
                ->andReturn($i);

            $child
                ->shouldReceive('save')
                ->once();

            $this->repository
                ->shouldReceive('findByParentId')
                ->once()
                ->with($i)
                ->andReturn(null);
        }

        $this->repository->recurse($this->model, function (Page $page) {
            $page->save();
        });
    }

    protected function withCurrentVersion()
    {
        $this->model
            ->shouldReceive('currentVersion')
            ->once()
            ->andReturnSelf();
    }
}
