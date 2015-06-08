<?php

use BoomCMS\Core\Template;
use BoomCMS\Core\Template\Manager;

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
    protected function getFilesystem()
    {
        return $this->getMock('Illuminate\Filesystem\Filesystem');
    }

    protected function getTemplateProvider()
    {
        return $this->getMock('BoomCMS\Core\Template\Provider');
    }

    public function testFindInstalledThemes()
    {
        $themes = ['test1', 'test2'];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('directories')
            ->with($this->equalTo('storage/boomcms/themes'))
            ->will($this->returnValue($themes));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals($themes, $manager->findInstalledThemes());
    }

    public function testFindNoInstalledThemes()
    {
        $themes = [];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('directories')
            ->with($this->equalTo('storage/boomcms/themes'))
            ->will($this->returnValue(null));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals($themes, $manager->findInstalledThemes());
    }

    public function testGetThemeDirectory()
    {
        $theme = 'test';

        $manager = new Manager($this->getFilesystem(), $this->getTemplateProvider(), false);
        $this->assertEquals('storage/boomcms/themes/' . $theme . '/views/templates', $manager->getThemeDirectory($theme));
    }

    public function testAvailableTemplates()
    {
        $templates = ['test1', 'test2'];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('files')
            ->with($this->equalTo('storage/boomcms/themes/test1/views/templates'))
            ->will($this->returnValue(['test1.php', 'test2.php']));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals($templates, $manager->findAvailableTemplates('test1'));
    }

    public function testAvailableTemplatesMustEndBePHP()
    {
        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('files')
            ->with($this->equalTo('storage/boomcms/themes/test1/views/templates'))
            ->will($this->returnValue(['test1.png', 'test2.php', 'test3']));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals(['test2'], $manager->findAvailableTemplates('test1'));
    }

    public function testNoAvailableTemplates()
    {
        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('files')
            ->with($this->equalTo('storage/boomcms/themes/test1/views/templates'))
            ->will($this->returnValue(null));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals([], $manager->findAvailableTemplates('test1'));
    }

    public function testTemplateIsInstalled()
    {
        $theme = $filename = 'test';
        $template = new Template\Template(['id' => 1]);

        $provider = $this->getTemplateProvider();
        $provider
            ->expects($this->once())
            ->method('findByThemeAndFilename')
            ->with($this->equalTo($theme), $this->equalTo($filename))
            ->will($this->returnValue($template));

        $manager = new Manager($this->getFilesystem(), $provider, false);
        $this->assertTrue($manager->templateIsInstalled($theme, $filename));
    }

    public function testTemplateIsNotInstalled()
    {
        $theme = $filename = 'test';
        $template = new Template\Template(['id' => 0]);

        $provider = $this->getTemplateProvider();
        $provider
            ->expects($this->once())
            ->method('findByThemeAndFilename')
            ->with($this->equalTo($theme), $this->equalTo($filename))
            ->will($this->returnValue($template));

        $manager = new Manager($this->getFilesystem(), $provider, false);
        $this->assertFalse($manager->templateIsInstalled($theme, $filename));
    }
}