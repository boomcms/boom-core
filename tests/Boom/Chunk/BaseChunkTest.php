<?php

use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\Lang;

class BaseChunkTest extends TestCase
{
    public function testGetPlaceholderTextForSlotnameIfDefined()
    {
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
        Lang::shouldReceive('get')
            ->once()
            ->with('boom::chunks.text.test')
            ->andReturn(null);

        Lang::shouldReceive('get')
            ->once()
            ->with('boom::chunks.text')
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
}