<?php

namespace BoomCMS\Tests;

use BoomCMS\Core\Template\Manager;
use BoomCMS\Core\Theme\Theme;
use BoomCMS\Database\Models\Template;
use BoomCMS\Repositories\Template as TemplateRepository;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class TemplateManagerTest extends AbstractTestCase
{
    protected function getFilesystem()
    {
        return $this->getMock(Filesystem::class);
    }

    protected function getTemplateRepository()
    {
        return m::mock(TemplateRepository::class);
    }

    public function testCreateTemplateWithFilename()
    {
        $theme = 'test';
        $filename = 'test_template';

        $repository = $this->getTemplateRepository();
        $repository->shouldReceive('create')->with([
            'name'     => 'Test Template',
            'theme'    => 'test',
            'filename' => 'test_template',
        ]);

        $manager = new Manager($this->getFilesystem(), $repository);
        $manager->createTemplateWithFilename($theme, $filename);
    }

    public function testfindAvailableThemes()
    {
        $themes = [new Theme('test1'), new Theme('test2')];

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('directories')
            ->with($this->equalTo(storage_path().'/boomcms/themes'))
            ->will($this->returnValue($themes));

        $manager = new Manager($filesystem, $this->getTemplateRepository(), false);
        $this->assertEquals([new Theme($themes[0]), new Theme($themes[1])], $manager->findAvailableThemes());
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), false);
        $this->assertEquals($themes, $manager->findAvailableThemes());
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), false);
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), false);
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), false);
        $this->assertEquals([], $manager->findAvailableTemplates(new Theme('test1')));
    }

    public function testTemplateIsInstalled()
    {
        $theme = $filename = 'test';
        $template = new Template(['id' => 1]);

        $provider = $this->getTemplateRepository();
        $provider
            ->shouldReceive('findByThemeAndFilename')
            ->with($theme, $filename)
            ->andReturn($template);

        $manager = new Manager($this->getFilesystem(), $provider, false);
        $this->assertTrue($manager->templateIsInstalled($theme, $filename));
    }

    public function testTemplateIsNotInstalled()
    {
        $theme = $filename = 'test';

        $provider = $this->getTemplateRepository();
        $provider
            ->shouldReceive('findByThemeAndFilename')
            ->with($theme, $filename)
            ->andReturn(null);

        $manager = new Manager($this->getFilesystem(), $provider, false);
        $this->assertFalse($manager->templateIsInstalled($theme, $filename));
    }
}
