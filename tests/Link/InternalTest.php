<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Database\Models\Page;
use BoomCMS\Link\Internal as Link;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class InternalTest extends AbstractTestCase
{
    public function testLeadingSlashIsRemovedExceptForRoot()
    {
        $links = [
            '/test' => 'test',
            '/'     => '/',
            'test'  => 'test',
        ];

        foreach ($links as $original => $uri) {
            PageFacade::shouldReceive('findByUri')
                ->once()
                ->with($uri);

            new Link($original);
        }
    }

    public function testUrlReturnsCorrectUrl()
    {
        $links = [
            '/test',
            '/test#test',
            '/test?test=test',
            '/test#test?test=test',
        ];

        $page = m::mock(Page::class.'[url]');
        $page
            ->shouldReceive('url')
            ->andReturn("http://{$this->baseUrl}/test");

        PageFacade::shouldReceive('findByUri')->with('test')->andReturn($page);

        foreach ($links as $l) {
            $link = new Link($l);

            $this->assertEquals("http://{$this->baseUrl}{$l}", $link->url());
        }

        return $page;
    }

    public function testGetTitleReturnsPageTitle()
    {
        $page = $this->getMock(Page::class, ['getTitle']);
        $page->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue('test'));

        PageFacade::shouldReceive('findByUri')->with('test')->andReturn($page);
        $link = new Link('test');

        $this->assertEquals('test', $link->getTitle());
    }

    public function testOriginalLinkIsReturnedIfPageNotFound()
    {
        $pageId = 1;

        PageFacade::shouldReceive('find')->with($pageId)->andReturn(null);

        $link = new Link($pageId);

        $this->assertEquals($pageId, $link->url());
    }

    /**
     * @depends testUrlReturnsCorrectUrl
     */
    public function testGetHostnameReturnsTheHostnameOfTheCurrentSite($page)
    {
        PageFacade::shouldReceive('findByUri')->with('test')->andReturn($page);

        $link = new Link('/test');

        $this->assertEquals($this->baseUrl, $link->getHostname());
    }
}
