<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Support\Helpers\URL;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Mockery as m;

class URLTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getHttpHost')->andReturn($this->baseUrl);
        RequestFacade::swap($request);
    }

    public function testGetAssetId()
    {
        $urls = [
            '/asset/1/view'                    => 1,
            '/asset/1'                         => 1,
            '/asset/1/view/2'                  => 1,
            '/asset'                           => 0,
            '/something-else'                  => 0,
            'http://www.othersite.com/asset/1' => 0,
        ];

        foreach ($urls as $path => $assetId) {
            $this->assertEquals($assetId, URL::getAssetId($path), $path);
        }
    }

    public function testFromTitle()
    {
        $base = '/blog';
        $title = 'Untitled';
        $url = '/blog/untitled';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($url)
            ->andReturn(true);

        $this->assertEquals($url, URL::fromTitle($base, $title));
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
                ->with($path)
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
            ->with('404')
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
            "https://{$this->baseUrl}/test" => '/test',
            "http://{$this->baseUrl}/test"  => '/test',
            "https://{$this->baseUrl}/"     => '/',
            "http://{$this->baseUrl}/"      => '/',
            'http://other.com/test'         => 'http://other.com/test',
            'https://other.com/test'        => 'https://other.com/test',
            '/test'                         => '/test',
        ];

        foreach ($urls as $original => $after) {
            $this->assertEquals($after, URL::makeRelative($original));
        }
    }
}
