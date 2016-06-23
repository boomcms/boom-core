<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Database\Models\Site;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Support\Helpers\URL;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Mockery as m;

class URLTest extends AbstractTestCase
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

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getHttpHost')->andReturn($this->baseUrl);
        RequestFacade::swap($request);
    }

    public function testFromTitle()
    {
        $base = '/blog';
        $title = 'Untitled';
        $url = '/blog/untitled';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($this->site, $url)
            ->andReturn(true);

        $this->assertEquals($url, URL::fromTitle($this->site, $base, $title));
    }

    public function testGetInternalPath()
    {
        $urls = [
            '/'         => '/',
            '/test'     => 'test',
            '/test?1=2' => 'test',
            '/?1=2'     => '/',
        ];

        foreach ($urls as $url => $path) {
            $this->assertEquals($path, URL::getInternalPath($url), $url);
        }
    }

    public function testIsInternalReturnsTrueForInternalUrlsWhichAreInUse()
    {
        $urls = [
            '/'                            => '/',
            '/test'                        => 'test',
            "http://{$this->baseUrl}/"     => '/',
            "https://{$this->baseUrl}/"    => '/',
            "http://{$this->baseUrl}/test" => 'test',
        ];

        foreach ($urls as $url => $path) {
            URLFacade::shouldReceive('isAvailable')
                ->once()
                ->with($this->site, $path)
                ->andReturn(false);

            $this->assertTrue(URL::isInternal($url));
        }
    }

    public function testIsInternalReturnsFalseForInternalUrlsWhichArentInUse()
    {
        $urls = [
            '/404',
            "http://{$this->baseUrl}/404",
        ];

        URLFacade::shouldReceive('isAvailable')
            ->twice()
            ->with($this->site, '404')
            ->andReturn(true);

        foreach ($urls as $url) {
            $this->assertFalse(URL::isInternal($url));
        }
    }

    public function testIsInternalReturnsFalseForExternalUrls()
    {
        $urls = [
            'http://other.com/test',
            'https://other.com/test',
        ];

        foreach ($urls as $url) {
            URLFacade::shouldReceive('isAvailable')->never();

            $this->assertFalse(URL::isInternal($url));
        }
    }

    public function testMakeRelative()
    {
        $urls = [
            "https://{$this->baseUrl}/test"   => '/test',
            "http://{$this->baseUrl}/test"    => '/test',
            "https://{$this->baseUrl}/"       => '/',
            "http://{$this->baseUrl}/"        => '/',
            'http://other.com/test'           => 'http://other.com/test',
            'https://other.com/test'          => 'https://other.com/test',
            '/test'                           => '/test',
        ];

        foreach ($urls as $original => $after) {
            $this->assertEquals($after, URL::makeRelative($original));
        }
    }
}
