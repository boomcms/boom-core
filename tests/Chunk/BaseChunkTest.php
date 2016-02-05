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
            ->getMock();

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
            ->getMock();

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
            ->setConstructorArgs([new Page(), [], 'test', true])
            ->getMock();

        $noteditable = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page(), [], 'test', false])
            ->getMock();

        $this->assertTrue($editable->isEditable());
        $this->assertFalse($noteditable->isEditable());
    }

    public function testReadonly()
    {
        $editable = $this->getMockBuilder(BaseChunk::class)
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page(), [], 'test', true])
            ->getMock();

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
            ->getMock();

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
            ->getMock();

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
            ->getMock();

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
            ->once()
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
}
