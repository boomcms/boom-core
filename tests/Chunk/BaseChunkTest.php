<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\BaseChunk;
use BoomCMS\Database\Models\Page;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\View;
use Mockery as m;

class BaseChunkTest extends AbstractTestCase
{
    public function testGetId()
    {
        $chunkId = 1;

        $chunk = m::mock(BaseChunk::class, [new Page(), ['id' => $chunkId], 'test', true])->makePartial();

        $this->assertEquals($chunkId, $chunk->getId());
    }

    public function testGetPlaceholderTextForSlotnameIfDefined()
    {
        Lang::shouldReceive('has')
            ->once()
            ->with('boomcms::chunks.text.test')
            ->andReturn(true);

        Lang::shouldReceive('get')
            ->once()
            ->with('boomcms::chunks.text.test')
            ->andReturn('some text');

        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test', true])->makePartial();

        $chunk
            ->shouldReceive('getType')
            ->once()
            ->andReturn('text');

        $this->assertEquals('some text', $chunk->getPlaceholderText());
    }

    public function testGetPlaceholderTextReturnsDefaultForType()
    {
        Lang::shouldReceive('has')
            ->once()
            ->with('boomcms::chunks.text.test')
            ->andReturn(false);

        Lang::shouldReceive('get')
            ->once()
            ->with('boomcms::chunks.text.default')
            ->andReturn('some text');

        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test', true])->makePartial();

        $chunk
            ->shouldReceive('getType')
            ->once()
            ->andReturn('text');

        $this->assertEquals('some text', $chunk->getPlaceholderText());
    }

    public function testDefaultPlaceholderTextsAreDefined()
    {
        $types = ['asset', 'location', 'text', 'html', 'library', 'linkset', 'slideshow', 'timestamp'];

        foreach ($types as $type) {
            $langKey = "boomcms::chunks.$type.default";
            $this->assertTrue(Lang::has($langKey), $type);
        }
    }

    public function testIsEditable()
    {
        $editable = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();
        $editable->editable(true);

        $noteditable = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();
        $noteditable->editable(false);

        $this->assertTrue($editable->isEditable());
        $this->assertFalse($noteditable->isEditable());
    }

    public function testReadonly()
    {
        $editable = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();
        $editable->editable(true);
        $readonly = $editable->readonly();

        $this->assertFalse($readonly->isEditable());
    }

    public function testGetRequiredAttributes()
    {
        $requiredAttributes = [
            'data-boom-chunk'         => 'testType',
            'data-boom-slot-name'     => 'test',
            'data-boom-slot-template' => 'default',
            'data-boom-page'          => 1,
            'data-boom-chunk-id'      => 2,
            'data-boom-has-content'   => 1,
        ];

        $chunk = m::mock(BaseChunk::class, [$this->validPage(), ['id' => 2], 'test', true])->makePartial();

        $chunk
            ->shouldReceive('getType')
            ->once()
            ->andReturn('testType');

        $chunk
            ->shouldReceive('hasContent')
            ->once()
            ->andReturn(true);

        $this->assertEquals($requiredAttributes, $chunk->getRequiredAttributes());
    }

    public function testAddAttributesToHtml()
    {
        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test', true])->makePartial();

        $chunk
            ->shouldReceive('getRequiredAttributes')
            ->once()
            ->andReturn(['required' => 'value1']);

        $chunk
            ->shouldReceive('attributes')
            ->once()
            ->andReturn(['attr' => 'value2']);

        $expected = '<p required="value1" attr="value2"></p>';
        $this->assertEquals($expected, $chunk->addAttributesToHtml('<p></p>'));
    }

    public function testGetTypeReturnsClassName()
    {
        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test', true])->makePartial();

        $this->assertEquals(strtolower(class_basename($chunk)), $chunk->getType());
    }

    public function testGetSlotname()
    {
        $slotname = 'test';
        $chunk = m::mock(BaseChunk::class, [new Page(), [], $slotname])->makePartial();

        $this->assertEquals($slotname, $chunk->getSlotname());
    }

    public function testParametersAreSetInView()
    {
        $params = ['key' => 'value'];
        $view = m::mock(View::class);
        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $chunk
            ->shouldReceive('hasContent')
            ->andReturn(true);

        $chunk
            ->shouldReceive('show')
            ->once()
            ->andReturn($view);

        $view
            ->shouldReceive('with')
            ->once()
            ->with($params);

        $chunk->params($params);
        $chunk->html();
    }

    public function testAfterDoesNotAppendWhenChunkHasNoHtml()
    {
        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();

        $chunk
            ->shouldReceive('html')
            ->once()
            ->andReturn('');

        $chunk->after('some content after');

        $this->assertEquals('', $chunk->render());
    }

    public function testAfterAppendsWhenChunkHasHtml()
    {
        $after = 'some content after';
        $html = 'some content';

        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();

        $chunk
            ->shouldReceive('html')
            ->once()
            ->andReturn($html);

        $chunk->after($after);

        $this->assertEquals($html.$after, $chunk->render());
    }

    public function testBeforeDoesNotPrependWhenChunkHasNoHtml()
    {
        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();

        $chunk
            ->shouldReceive('html')
            ->once()
            ->andReturn('');

        $chunk->before('some content before');

        $this->assertEquals('', $chunk->render());
    }

    public function testBeforePrependsWhenChunkHasHtml()
    {
        $before = 'some content before';
        $html = 'some content';

        $chunk = m::mock(BaseChunk::class, [new Page(), [], 'test'])->makePartial();

        $chunk
            ->shouldReceive('html')
            ->once()
            ->andReturn($html);

        $chunk->before($before);

        $this->assertEquals($before.$html, $chunk->render());
    }
}
