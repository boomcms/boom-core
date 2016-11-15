<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Link;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;
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

        URL::shouldReceive('isAvailable')
            ->times(5)
            ->with('test')
            ->andReturn(false);

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
            'http://www.google.com/test',
        ];

        URL::shouldReceive('isAvailable')
            ->once()
            ->with('test')
            ->andReturn(true);

        foreach ($internalLinks as $link) {
            $this->assertInstanceOf(Link\External::class, Link\Link::factory($link), $link);
        }
    }

    public function testGetQueryReturnsArray()
    {
        $queries = [
            ''         => [],
            '?'        => [],
            '?a=b&c=d' => ['a' => 'b', 'c' => 'd'],
        ];

        foreach ($queries as $string => $array) {
            $url = "$this->baseUrl/test$string";
            $link = m::mock(Link\Link::class, [$url])->makePartial();

            $this->assertEquals($array, $link->getQuery());
        }
    }

    public function testGetPath()
    {
        $url = "http://$this->baseUrl/test";
        $link = m::mock(Link\Link::class)->makePartial();

        $link
            ->shouldReceive('url')
            ->once()
            ->andReturn($url);

        $this->assertEquals('/test', $link->getPath());
    }

    public function testGetParameterReturnsValueOrNull()
    {
        $query = '?a=b&c=d';

        $url = "$this->baseUrl/test$query";
        $link = m::mock(Link\Link::class, [$url])->makePartial();

        $this->assertNull($link->getParameter('invalid'));
        $this->assertEquals('b', $link->getParameter('a'));
    }
}
