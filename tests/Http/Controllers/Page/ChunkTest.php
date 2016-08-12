<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Chunk\Text;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Http\Controllers\Page\Chunk as Controller;
use BoomCMS\Support\Facades\Chunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use Mockery as m;

class ChunkTest extends BaseControllerTest
{
    protected $className = Controller::class;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var PageVersion
     */
    protected $version;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class);
        $this->version = m::mock(PageVersion::class);

        $this->page
            ->shouldReceive('getCurrentVersion')
            ->andReturn($this->version);

        $this->version
            ->shouldReceive('getStatus')
            ->andReturn('draft');

        $this->requireRole('edit', $this->page);
    }

    public function testSaveNoConflict()
    {
        $chunk = m::mock(Text::class)->makePartial();

        $chunk
            ->shouldReceive('render')
            ->twice()
            ->andReturn('{html}');

        $chunk
            ->shouldReceive('getId')
            ->andReturn(1);

        $attrs = [
            'type'     => 'text',
            'slotname' => 'standfirst',
            'text'     => 'test',
            'chunkId'  => $chunk->getId(),
        ];

        Chunk::shouldReceive('get')
            ->once()
            ->with($attrs['type'], $attrs['slotname'], $this->page)
            ->andReturn($chunk);

        Chunk::shouldReceive('create')
            ->once()
            ->with($this->page, array_except($attrs, ['chunkId']))
            ->andReturn($chunk);

        $response = $this->controller->postSave(new Request($attrs), $this->page);

        $this->assertEquals($this->version->getStatus(), $response['status']);
        $this->assertEquals($chunk->getId(), $response['chunkId']);
        $this->assertEquals($chunk->render(), $response['html']);
    }

    public function testSaveConflictNotForced()
    {
        $chunk = m::mock(Text::class)->makePartial();
        $latest = m::mock(Text::class)->makePartial();

        $latest
            ->shouldReceive('render')
            ->andReturn('{html}');

        $latest
            ->shouldReceive('getId')
            ->andReturn(2);

        $chunk
            ->shouldReceive('getId')
            ->andReturn(1);

        $attrs = [
            'type'     => 'text',
            'slotname' => 'standfirst',
            'text'     => 'test',
            'chunkId'  => $chunk->getId(),
        ];

        Chunk::shouldReceive('get')
            ->once()
            ->with($attrs['type'], $attrs['slotname'], $this->page)
            ->andReturn($latest);

        Chunk::shouldReceive('create')->never();

        $view = m::mock(View::class);
        $view->shouldReceive('render');

        ViewFacade::shouldReceive('make')->andReturn($view);

        $response = $this->controller->postSave(new Request($attrs), $this->page);
        $data = json_decode($response->getContent());

        $this->assertEquals(500, $response->status());
        $this->assertEquals($this->version->getStatus(), $data->status);
        $this->assertEquals($latest->getId(), $data->chunkId);
        $this->assertEquals($latest->render(), $data->chunk);
        $this->assertEquals('conflict', $data->error);
    }

    public function testSaveConflictForceSaved()
    {
        $chunk = m::mock(Text::class)->makePartial();

        $chunk
            ->shouldReceive('render')
            ->andReturn('{html}');

        $chunk
            ->shouldReceive('getId')
            ->andReturn(1);

        $attrs = [
            'type'     => 'text',
            'slotname' => 'standfirst',
            'text'     => 'test',
            'chunkId'  => $chunk->getId(),
            'force'    => true,
        ];

        Chunk::shouldReceive('get')->never();

        Chunk::shouldReceive('create')
            ->once()
            ->with($this->page, array_except($attrs, ['chunkId', 'force']))
            ->andReturn($chunk);

        $response = $this->controller->postSave(new Request($attrs), $this->page);

        $this->assertEquals($this->version->getStatus(), $response['status']);
        $this->assertEquals($chunk->getId(), $response['chunkId']);
        $this->assertEquals($chunk->render(), $response['html']);
    }
}
