<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Link;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class BaseLinkTest extends AbstractTestCase
{
    public function testFactoryReturnsInternalLink()
    {
        $internalLinks = [
            '1',
            1,
            '/test',
            "http://{$this->baseUrl}/test",
            "https://{$this->baseUrl}/test",
            "http://{$this->baseUrl}/test?test=test",
            "http://{$this->baseUrl}/test#test",
        ];

        Page::shouldReceive('find')
            ->with(m::any(1, '1'))
            ->andReturn($this->validPage());
            
        Page::shouldReceive('findByUri')
            ->with('test')
            ->andReturn($this->validPage());

        foreach ($internalLinks as $link) {
            $this->assertInstanceOf(Link\Internal::class, Link\Link::factory($link), $link);
        }
    }

    public function testFactoryReturnsExternalLink()
    {
        $internalLinks = [
            '/test', // Relative link but the page doens't exist
            "http://www.google.com/test",
        ];
            
        Page::shouldReceive('findByUri')
            ->with('test')
            ->andReturn($this->invalidPage());

        foreach ($internalLinks as $link) {
            $this->assertInstanceOf(Link\External::class, Link\Link::factory($link), $link);
        }
    }
}