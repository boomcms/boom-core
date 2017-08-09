<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Link;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class BaseLinkTest extends AbstractTestCase
{
    public function testFactoryReturnsPageLink()
    {
        $pageLinks = [
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

        foreach ($pageLinks as $link) {
            $this->assertInstanceOf(Link\PageLink::class, Link\Link::factory($link), $link);
        }
    }

    public function testFactoryReturnsAssetLink()
    {
        $assetLinks = [
            '/asset/1',
            '/asset/1/view',
            '/asset/1/download',
            "http://{$this->baseUrl}/asset/1",
            "https://{$this->baseUrl}/asset/1/view",
            "http://{$this->baseUrl}/asset/1/download",
        ];

        $asset = new Asset();

        AssetFacade::shouldReceive('find')
            ->with(1)
            ->andReturn($asset);

        foreach ($assetLinks as $link) {
            $this->assertInstanceOf(Link\AssetLink::class, Link\Link::factory($link), $link);
        }
    }

    public function testFactoryReturnsExternalLink()
    {
        $externalLinks = [
            '/test', // Relative link but the page doens't exist
            'http://www.boomcms.net/test',
        ];

        URL::shouldReceive('isAvailable')
            ->once()
            ->with('test')
            ->andReturn(true);

        foreach ($externalLinks as $link) {
            $this->assertInstanceOf(Link\External::class, Link\Link::factory($link), $link);
        }
    }

    /**
     * @depends testFactoryReturnsExternalLink
     */
    public function testFactoryResturnsLinkWithAttributes()
    {
        $url = 'http://www.boomcms.net';
        $attrs = [
            'text' => 'link text',
        ];

        $link = Link\Link::factory($url, $attrs);

        $this->assertEquals($attrs, $link->getAttributes());
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
