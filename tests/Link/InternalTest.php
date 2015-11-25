<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Core\Page\Page;
use BoomCMS\Link\Internal as Link;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Tests\AbstractTestCase;

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

        $page = $this->getMock(Page::class, ['url']);
        $page->expects($this->any())
            ->method('url')
            ->will($this->returnValue("http://{$this->baseUrl}/test"));

        PageFacade::shouldReceive('findByUri')->with('test')->andReturn($page);

        foreach ($links as $l) {
            $link = new Link($l);

            $this->assertEquals("http://{$this->baseUrl}{$l}", $link->url());
        }
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
}
