<?php

use BoomCMS\Core\Template;
use BoomCMS\Core\Template\Manager;

use Illuminate\Support\Facades\View;

class TemplateTest extends PHPUnit_Framework_TestCase
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
        $filename = 'storage/boomcms/themes/test/src/views/templates/test';
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

    protected function getTemplate(array $attrs = [])
    {
        return new Template\Template($attrs);
    }
}