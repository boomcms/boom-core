<?php

use BoomCMS\Core\Template;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class TemplateTest extends TestCase
{
    public function testLoadedIfHasId()
    {
        $template = new Template\Template(['id' => 2]);

        $this->assertTrue($template->loaded());
    }

    public function testNotLoadedIfNoId()
    {
        $template = new Template\Template([]);

        $this->assertFalse($template->loaded());
    }

    public function testGetThemeName()
    {
        $template = $this->getTemplate(['theme' => 'test']);

        $this->assertEquals('test', $template->getThemeName());
    }

    public function testGetTheme()
    {
        $template = $this->getTemplate(['theme' => 'test']);

        $this->assertInstanceOf('BoomCMS\Core\Theme\Theme', $template->getTheme());
    }

    public function testGetFullFilename()
    {
        $filename = 'test::templates.test';
        $template = $this->getTemplate(['theme' => 'test', 'filename' => 'test']);

        $this->assertEquals($filename, $template->getFullFilename());
    }

    public function testFileExists()
    {
        $template = $this->getTemplate(['theme' => 'test', 'filename' => 'test']);

        View::shouldReceive('exists')
            ->once()
            ->with($template->getFullFilename())
            ->andReturn(false);

        $this->assertFalse($template->fileExists());

        View::shouldReceive('exists')
            ->once()
            ->with($template->getFullFilename())
            ->andReturn(true);

        $this->assertTrue($template->fileExists());
    }

    public function testGetViewName()
    {
        $template = $this->getTemplate(['theme' => 'test', 'filename' => 'test']);

        $this->assertEquals('test:templates.test', $template->getViewName());
    }

    public function testGetConfigReturnsThemeConfigMergedWithTemplateConfig()
    {
        $template = $this->getTemplate(['theme' => 'test', 'filename' => 'test']);

        Config::shouldReceive('get')
            ->with('boomcms.themes.test.*')
            ->once()
            ->andReturn(['key1' => 'theme', 'key2' => 'theme']);

        Config::shouldReceive('get')
            ->with('boomcms.themes.test.test')
            ->once()
            ->andReturn(['key2' => 'template', 'key3' => 'template']);

        $this->assertEquals([
            'key1' => 'theme',
            'key2' => 'template',
            'key3' => 'template',
        ], $template->getConfig());
    }

    public function testGetConfigReturnsArray()
    {
        $template = $this->getTemplate(['theme' => 'test', 'filename' => 'test']);

        Config::shouldReceive('get')
            ->with('boomcms.themes.test.*')
            ->once()
            ->andReturn(null);

        Config::shouldReceive('get')
            ->with('boomcms.themes.test.test')
            ->once()
            ->andReturn(null);

        $this->assertEquals([], $template->getConfig());
    }

    public function testGetChunksAlwaysReturnsArray()
    {
        $template = $this->getMockBuilder('BoomCMS\Core\Template\Template')
            ->setMethods(['getConfig'])
            ->disableOriginalConstructor()
            ->getMock();

        $template
            ->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue([]));

        $this->assertEquals([], $template->getChunks());
    }

    protected function getTemplate(array $attrs = [])
    {
        return new Template\Template($attrs);
    }
}
