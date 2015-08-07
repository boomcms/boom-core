<?php

use BoomCMS\Core\Chunk\BaseChunk;
use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\Lang;

class BaseChunkTest extends TestCase
{
    public function testGetPlaceholderTextForSlotnameIfDefined()
    {
        Lang::shouldReceive('has')
            ->once()
            ->with('boom::chunks.text.test')
            ->andReturn(true);

        Lang::shouldReceive('get')
            ->once()
            ->with('boom::chunks.text.test')
            ->andReturn('some text');

        $chunk = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([new Page([]), [], 'test', true])
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
            ->with('boom::chunks.text.test')
            ->andReturn(false);

        Lang::shouldReceive('get')
            ->once()
            ->with('boom::chunks.text.default')
            ->andReturn('some text');

        $chunk = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([new Page([]), [], 'test', true])
            ->getMock();

        $chunk
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('text'));

        $this->assertEquals('some text', $chunk->getPlaceholderText());
    }

    public function testDefaultPlaceholderTextsAreDefined()
    {
        foreach (BaseChunk::$types as $type) {
            $langKey = "boom::chunks.$type.default";
            $this->assertTrue(Lang::has($langKey), $type);
        }
    }

    public function testIsEditable()
    {
        $editable = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page([]), [], 'test', true])
            ->getMock();

        $noteditable = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page([]), [], 'test', false])
            ->getMock();

        $this->assertTrue($editable->isEditable());
        $this->assertFalse($noteditable->isEditable());
    }

    public function testReadonly()
    {
        $editable = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent'])
            ->setConstructorArgs([new Page([]), [], 'test', true])
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

        $chunk = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent', 'getType'])
            ->setConstructorArgs([new Page(['id' => 1]), ['id' => 2], 'test', true])
            ->getMock();

        $chunk
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('testType'));

        $this->assertEquals($requiredAttributes, $chunk->getRequiredAttributes());
    }

    public function testAddAttributesToHtml()
    {
        $chunk = $this->getMockBuilder('BoomCMS\Core\Chunk\BaseChunk')
            ->setMethods(['show', 'showDefault', 'hasContent', 'getRequiredAttributes', 'attributes'])
            ->setConstructorArgs([new Page([]), [], 'test', true])
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
}
