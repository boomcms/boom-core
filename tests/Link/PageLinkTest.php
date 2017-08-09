<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Chunk\Text as TextChunk;
use BoomCMS\Database\Models\Page;
use BoomCMS\Link\PageLink as Link;
use BoomCMS\Support\Facades\Chunk;
use BoomCMS\Support\Facades\Page as PageFacade;
use Mockery as m;

class PageLinkTest extends InternalTest
{
    protected $linkClass = Link::class;
    protected $objectClass = Page::class;

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

    public function testOriginalLinkIsReturnedIfPageNotFound()
    {
        $pageId = 1;

        PageFacade::shouldReceive('find')->with($pageId)->andReturn(null);

        $link = new Link($pageId);

        $this->assertEquals($pageId, $link->url());
    }

    public function testGetHostnameReturnsTheHostnameOfTheCurrentSite()
    {
        $page = m::mock(Page::class.'[url]');
        $page
            ->shouldReceive('url')
            ->andReturn("http://{$this->baseUrl}/test");

        PageFacade::shouldReceive('findByUri')->once()->with('test')->andReturn($page);

        $link = new Link('/test');

        $this->assertEquals($this->baseUrl, $link->getHostname());
    }

    public function testGetFeatureImageIdReturnsPageFeatureImageId()
    {
        $attrs = [
            ['asset_id' => null],
            ['asset_id' => ''],
            ['asset_id' => 0],
            [],
        ];

        foreach ($attrs as $a) {
            $assetId = 1;
            $page = new Page([Page::ATTR_FEATURE_IMAGE => $assetId]);
            $link = new Link($page, $a);

            $this->assertEquals($assetId, $link->getFeatureImageId());
        }
    }

    public function testGetPageWithInjectedPage()
    {
        $page = new Page();
        $link = new Link($page);

        $this->assertEquals($page, $link->getPage());
    }

    public function testGetPageFromPageId()
    {
        $page = new Page();
        $pageId = 1;

        PageFacade::shouldReceive('find')->once()->with($pageId)->andReturn($page);

        $link = new Link($pageId);

        $this->assertEquals($page, $link->getPage());
    }

    public function testGetPageFromPageUrl()
    {
        $page = new Page();

        PageFacade::shouldReceive('findByUri')->once()->with('test')->andReturn($page);

        $link = new Link('/test');

        $this->assertEquals($page, $link->getPage());
    }
    public function testGetTitleReturnsPageTitle()
    {
        $attrs = [
            ['title' => null],
            ['title' => ''],
            [],
        ];

        foreach ($attrs as $a) {
            $title = 'page title';
            $page = m::mock(Page::class);
            $page->shouldReceive('getTitle')->once()->andReturn($title);

            $link = new Link($page, $a);

            $this->assertEquals($title, $link->getTitle());
        }
    }

    public function testGetTextReturnsPageStandfirst()
    {
        $attrs = [
            ['text' => null],
            ['text' => ''],
            [],
        ];

        foreach ($attrs as $a) {
            $text = 'page standfirst';
            $page = new Page();

            $chunk = new TextChunk($page, ['text' => $text, 'site_text' => $text], 'standfirst');

            Chunk::shouldReceive('get')
                ->once()
                ->with('text', 'standfirst', $page)
                ->andReturn($chunk);

            $link = new Link($page, $a);

            $this->assertEquals($text, $link->getText());
        }
    }

    public function testIsValidReturnsFalseIfNoPage()
    {
        $pageId = 1;

        PageFacade::shouldReceive('find')
            ->once()
            ->with($pageId)
            ->andReturn(null);

        $link = new Link($pageId);

        $this->assertFalse($link->isValid());
    }

    public function testIsValidDependsOnWhetherPageIsDeleted()
    {
        $page = m::mock(Page::class);

        foreach ([true, false] as $deleted) {
            $page->shouldReceive('isDeleted')->once()->andReturn($deleted);

            $link = new Link($page);

            $this->assertEquals(!$deleted, $link->isValid());
        }
    }

    public function testIsVisibleIfPageIsVisible()
    {
        $page = m::mock(Page::class);

        foreach ([true, false] as $visible) {
            $page->shouldReceive('isVisible')->once()->andReturn($visible);

            $link = new Link($page);

            $this->assertEquals($visible, $link->isVisible());
        }
    }
}
