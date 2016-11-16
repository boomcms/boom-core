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

        $chunk = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([new Page(), ['id' => $chunkId], 'test', true])
            ->createMock();

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

        $chunk = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([new Page(), [], 'test', true])
            ->createMock();

        $chunk
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('text'));

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

        $chunk = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([new Page(), [], 'test', true])
            ->createMock();

        $chunk
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('text'));

        $this->assertEquals('some text', $chunk->getPlaceholderText());
    }

    public function testDefaultPlaceholderTextsAreDefined()
    {
        $types = ['asset', 'location', 'link', 'text', 'html', 'feature', 'library', 'linkset', 'slideshow', 'timestamp'];

        foreach ($types as $type) {
            $langKey = "boomcms::chunks.$type.default";
            $this->assertTrue(Lang::has($langKey), $type);
        }
    }

    public function testIsEditable()
    {
        $editable = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page(), [], 'test'])
            ->createMock();

        $editable->editable(true);

        $noteditable = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page(), [], 'test'])
            ->createMock();

        $noteditable->editable(false);

        $this->assertTrue($editable->isEditable());
        $this->assertFalse($noteditable->isEditable());
    }

    public function testReadonly()
    {
        $editable = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page(), [], 'test'])
            ->createMock();

        $editable->editable(true);
        $readonly = $editable->readonly();

        $this->assertFalse($readonly->isEditable());
    }

    public function testGetRequiredAttributes()
    {
        $requiredAttributes = [
            'data-boom-chunk'         => 'testType',
            'data-boom-slot-name'     => 'test',
            'data-boom-slot-template' => null,
            'data-boom-page'          => 1,
            'data-boom-chunk-id'      => 2,
        ];

        $chunk = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([$this->validPage(), ['id' => 2], 'test', true])
            ->createMock();

        $chunk
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('testType'));

        $this->assertEquals($requiredAttributes, $chunk->getRequiredAttributes());
    }

    public function testAddAttributesToHtml()
    {
        $chunk = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent', 'getRequiredAttributes', 'attributes'])
            ->setConstructorArgs([new Page(), [], 'test', true])
            ->createMock();

        $chunk
            ->expects($this->once())
            ->method('getRequiredAttributes')
            ->will($this->returnValue(['required' => 'value1']));

        $chunk
            ->expects($this->once())
            ->method('attributes')
            ->will($this->returnValue(['attr' => 'value2']));

        $expected = '<p required="value1" attr="value2"></p>';
        $this->assertEquals($expected, $chunk->addAttributesToHtml('<p></p>'));
    }

    public function testGetTypeReturnsClassName()
    {
        $chunk = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page(), [], 'test', true])
            ->createMock();

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
