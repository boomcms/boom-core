<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Database\Models\Site;
use BoomCMS\Link;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Facades\URL;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class BaseLinkTest extends AbstractTestCase
{
    /**
     * @var Site
     */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = new Site([Site::ATTR_HOSTNAME => $this->baseUrl]);
        Router::shouldReceive('getActiveSite')->andReturn($this->site);
    }

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
            ->with($this->site, 'test')
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
            ->with($this->site, 'test')
            ->andReturn(true);

        foreach ($internalLinks as $link) {
            $this->assertInstanceOf(Link\External::class, Link\Link::factory($link), $link);
        }
    }
}
