<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Template;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Support\Facades\Settings;
use BoomCMS\Support\Helpers;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class HelpersTest extends AbstractTestCase
{
    public function testAnalyticsReturnsAnalyticsSettingInProduction()
    {
        App::shouldReceive('environment')->andReturn('production');
        Settings::shouldReceive('get')->with('analytics')->andReturn('test');

        $this->assertEquals('test', Helpers::analytics());
    }

    public function testAnalyticsReturnsNullIfNotInProduction()
    {
        App::shouldReceive('environment')->andReturn('local');
        Settings::shouldReceive('get')->with('analytics')->andReturn('test');

        $this->assertEquals('', Helpers::analytics());
    }

    public function testViewWithNamespaceGiven()
    {
        View::shouldReceive('make')->with('namespace::name', [])->andReturn('view');

        $this->assertEquals('view', Helpers::view('name', [], 'namespace'));
    }

    public function testViewUsesNamespaceOfActivePageTemplate()
    {
        $template = new Template(['theme' => 'test']);
        $page = $this->getMock(Page::class, ['getTemplate']);
        $page
            ->expects($this->once())
            ->method('getTemplate')
            ->will($this->returnValue($template));

        Router::shouldReceive('getActivePage')->andReturn($page);

        View::shouldReceive('make')->with('test::name', [])->andReturn('view');

        $this->assertEquals('view', Helpers::view('name'));
    }

    public function testPubWithNamespaceGiven()
    {
        $this->assertEquals('/vendor/boomcms/themes/test/file.png', Helpers::pub('file.png', 'test'));
    }

    public function testPubRemovesExtraneousSlash()
    {
        $this->assertEquals('/vendor/boomcms/themes/test/file.png', Helpers::pub('/file.png', 'test'));
    }

    public function testPubRemovesWhitespace()
    {
        $this->assertEquals('/vendor/boomcms/themes/test/file.png', Helpers::pub(' file.png ', 'test'));
    }

    public function testPubUsesNamespaceOfActivePageTemplate()
    {
        $template = new Template(['theme' => 'test']);
        $page = $this->getMock(Page::class, ['getTemplate']);
        $page
            ->expects($this->once())
            ->method('getTemplate')
            ->willReturn($template);

        Router::shouldReceive('getActivePage')->andReturn($page);

        $this->assertEquals('/vendor/boomcms/themes/test/file.png', Helpers::pub('file.png'));
    }
}
