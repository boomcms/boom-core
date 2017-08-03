<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Linkset;
use BoomCMS\Database\Models\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Facades\URL;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class ChunkLinksetTest extends AbstractTestCase
{
    public function testGetLinksRemovesLinksWithoutPageIdOrUrl()
    {
        $chunk = $this->chunk(['links' => [['title' => 'Invalid link']]]);

        $this->assertEquals([], $chunk->getLinks());
    }

    public function testGetLinksReturnsEmptyArrayWhenNoLinksAreGiven()
    {
        $chunk = $this->chunk();

        $this->assertEquals([], $chunk->getLinks());
    }

    public function testGetLinksRemovesDeletedPageWhenEditable()
    {
        $pageId = 1;

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('isDeleted')
            ->once()
            ->andReturn(true);

        PageFacade::shouldReceive('find')
            ->once()
            ->with($pageId)
            ->andReturn($page);

        $chunk = $this->chunk(['links' => [['target_page_id' => $pageId]]]);
        $chunk->editable(true);

        $this->assertEquals([], $chunk->getLinks());
    }

    public function testGetLinksRemovesDeletedPageWhenNotEditable()
    {
        $path = 'test';

        $page = m::mock(Page::class);
        $page->shouldReceive('isDeleted')->once()->andReturn(true);

        URL::shouldReceive('isAvailable')
            ->once()
            ->with($path)
            ->andReturn(false);

        PageFacade::shouldReceive('findByUri')
            ->once()
            ->with($path)
            ->andReturn($page);

        $chunk = $this->chunk(['links' => [['url' => '/test']]]);

        $this->assertEquals([], $chunk->getLinks());
    }

    public function testGetLinksIgnoresPageVisibilityWhenEditable()
    {
        $path = 'test';

        $page = m::mock(Page::class);
        $page->shouldReceive('isVisible')->never();
        $page->shouldReceive('isDeleted')->once()->andReturn(false);

        URL::shouldReceive('isAvailable')
            ->once()
            ->with($path)
            ->andReturn(false);

        PageFacade::shouldReceive('findByUri')
            ->once()
            ->with($path)
            ->andReturn($page);

        $chunk = $this->chunk(['links' => [['url' => '/test']]]);
        $chunk->editable(true);
        $links = $chunk->getLinks();

        $this->assertEquals(1, count($links));
        $this->assertEquals($page, $links[0]->getPage());

        return $page;
    }

    public function testGetLinksRemovesInvisiblePageWhenNotEditable()
    {
        $path = 'test';

        $page = m::mock(Page::class);
        $page->shouldReceive('isVisible')
            ->once()
            ->andReturn(false);

        $page->shouldReceive('isDeleted')
            ->once()
            ->andReturn(false);

        URL::shouldReceive('isAvailable')
            ->once()
            ->with($path)
            ->andReturn(false);

        PageFacade::shouldReceive('findByUri')
            ->once()
            ->with($path)
            ->andReturn($page);

        $chunk = $this->chunk(['links' => [['url' => '/test']]], false);

        $this->assertEquals([], $chunk->getLinks());
    }

    protected function chunk(array $attrs = [], $editable = false)
    {
        return new Linkset(new Page(), $attrs, 'test', $editable);
    }
}
