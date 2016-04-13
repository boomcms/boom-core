<?php

namespace BoomCMS\Tests\Support\Helpers;

use BoomCMS\Database\Models\Site;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Support\Helpers\URL;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Mockery as m;

class URLTest extends AbstractTestCase
{
    public function testFromTitle()
    {
        $site = new Site();
        $base = '/blog';
        $title = 'Untitled';
        $url = '/blog/untitled';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $url)
            ->andReturn(true);

        $this->assertEquals($url, URL::fromTitle($site, $base, $title));
    }

    public function testMakeUniqueWithUniqueUrl()
    {
        $site = new Site();
        $location = 'test';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $location)
            ->andReturn(true);

        $this->assertEquals($location, URL::makeUnique($site, $location));
    }

    public function testMakeUniqueAppendsIncrementalNumber()
    {
        $site = new Site();
        $location = 'test';
        $unique = 'test1';

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $location)
            ->andReturn(false);

        URLFacade::shouldReceive('isAvailable')
            ->once()
            ->with($site, $unique)
            ->andReturn(true);

        $this->assertEquals($unique, URL::makeUnique($site, $location));
    }

    public function testMakeRelative()
    {
        $host = 'test.com';

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getHttpHost')->andReturn($host);
        RequestFacade::swap($request);

        $urls = [
            "https://{$host}/test"   => '/test',
            "http://{$host}/test"    => '/test',
            "https://{$host}/"       => '/',
            "http://{$host}/"        => '/',
            'http://other.com/test'  => 'http://other.com/test',
            'https://other.com/test' => 'https://other.com/test',
            '/test'                  => '/test',
        ];

        foreach ($urls as $original => $after) {
            $this->assertEquals($after, URL::makeRelative($original));
        }
    }
}
