<?php

namespace BoomCMS\Tests\Chunk\Slideshow;

use BoomCMS\Chunk\Linkset\Link;
use BoomCMS\Tests\AbstractTestCase;

class LinkTest extends AbstractTestCase
{
    public function testGetReturnsEmptyString()
    {
        $link = new Link([]);

        $this->assertEquals('', $link->getText());
    }

    public function testGetTextReturnsGivenText()
    {
        $text = 'test';
        $link = new Link(['text' => $text]);

        $this->assertEquals($text, $link->getText());
    }
}
