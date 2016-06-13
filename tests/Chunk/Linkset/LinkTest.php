<?php

namespace BoomCMS\Tests\Chunk\Slideshow;

use BoomCMS\Chunk\Linkset\Link;
use BoomCMS\Chunk\Text as TextChunk;
use BoomCMS\Link\Internal as InternalLink;
use BoomCMS\Support\Facades\Chunk;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class LinkTest extends AbstractTestCase
{
    public function testSetLink()
    {
        $internal = m::mock(InternalLink::class);
        $link = new Link(['link' => $internal]);

        $this->assertEquals($internal, $link->getLink());
    }

    public function testTextAttributeReturnsEmptyString()
    {
        $link = new Link([]);

        $this->assertEquals('', $link->getTextAttribute());
    }

    public function testTextAttributeReturnsText()
    {
        $text = 'test';

        $link = new Link(['text' => $text]);

        $this->assertEquals($text, $link->getTextAttribute());
    }

    public function testGetTextReturnsEmptyString()
    {
        $link = new Link(['url' => 'http://www.test.com']);

        $this->assertEquals('', $link->getText());
    }

    public function testGetTextReturnsStandfirstForInternalLink()
    {
        $page = $this->validPage();
        $standfirst = 'test';
        $text = new TextChunk($page, [
            'slotname'  => 'standfirst',
            'text'      => $standfirst,
            'site_text' => $standfirst, ],
        'test', false);

        $internal = m::mock(InternalLink::class)->makePartial();
        $internal->shouldReceive('getPage')->once()->andReturn($page);

        Chunk::shouldReceive('get')
            ->once()
            ->with('text', 'standfirst', $page)
            ->andReturn($text);

        $link = new Link(['link' => $internal]);

        $this->assertEquals($standfirst, $link->getText());
    }

    public function testGetTextReturnsGivenText()
    {
        $text = 'test';
        $link = new Link(['text' => $text]);

        $this->assertEquals($text, $link->getText());
    }
}
