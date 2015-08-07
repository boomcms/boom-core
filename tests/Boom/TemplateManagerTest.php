<?php

use BoomCMS\Core\Template;
use BoomCMS\Core\Template\Manager;
use BoomCMS\Core\Theme\Theme;

class TemplateManagerTest extends TestCase
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
        $themes = [new Theme('test1'), new Theme('test2')];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('directories')
            ->with($this->equalTo(storage_path().'/boomcms/themes'))
            ->will($this->returnValue($themes));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals([new Theme($themes[0]), new Theme($themes[1])], $manager->findInstalledThemes());
    }

    public function testFindNoInstalledThemes()
    {
        $themes = [];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('directories')
            ->with($this->equalTo(storage_path().'/boomcms/themes'))
            ->will($this->returnValue(null));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals($themes, $manager->findInstalledThemes());
    }

    public function testAvailableTemplates()
    {
        $templates = ['test1', 'test2'];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('files')
            ->with($this->equalTo(storage_path().'/boomcms/themes/test1/src/views/templates'))
            ->will($this->returnValue(['test1.php', 'test2.php']));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals($templates, $manager->findAvailableTemplates(new Theme('test1')));
    }

    public function testAvailableTemplatesMustEndBePHP()
    {
        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('files')
            ->with($this->equalTo(storage_path().'/boomcms/themes/test1/src/views/templates'))
            ->will($this->returnValue(['test1.png', 'test2.php', 'test3']));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals(['test2'], $manager->findAvailableTemplates(new Theme('test1')));
    }

    public function testNoAvailableTemplates()
    {
        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('files')
            ->with($this->equalTo(storage_path().'/boomcms/themes/test1/src/views/templates'))
            ->will($this->returnValue(null));

        $manager = new Manager($filesystem, $this->getTemplateProvider(), false);
        $this->assertEquals([], $manager->findAvailableTemplates(new Theme('test1')));
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
