<?php

namespace BoomCMS\Tests;

use BoomCMS\Core\Template\Manager;
use BoomCMS\Database\Models\Template;
use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Theme\Theme;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class TemplateManagerTest extends AbstractTestCase
{
    /**
     * @var Cache
     */
    protected $cache;

    public function setUp()
    {
        parent::setUp();

        $this->cache = m::mock(Cache::class)->makePartial();
    }

    protected function getFilesystem()
    {
        return $this->createMock(Filesystem::class);
    }

    protected function getTemplateRepository()
    {
        return m::mock(TemplateRepository::class);
    }

    public function testCreateTemplateWithFilename()
    {
        $theme = 'test';
        $filename = 'test_temp-late';

        $repository = $this->getTemplateRepository();
        $repository->shouldReceive('create')->with([
            'name'     => 'Test Temp Late',
            'theme'    => $theme,
            'filename' => $filename,
        ]);

        $manager = new Manager($this->getFilesystem(), $repository, $this->cache);
        $manager->createTemplateWithFilename($theme, $filename);
    }

    public function testFindAndInstallThemes()
    {
        $themes = [new Theme('test1'), new Theme('test2')];

        $this->cache
            ->shouldReceive('forever')
            ->once()
            ->with('installedThemes', $themes);

        $filesystem = $this->getFilesystem();
        $filesystem
            ->expects($this->once())
            ->method('directories')
            ->with($this->equalTo(storage_path().'/boomcms/themes'))
            ->will($this->returnValue($themes));

        $manager = new Manager($filesystem, $this->getTemplateRepository(), $this->cache);
        $this->assertEquals([new Theme($themes[0]), new Theme($themes[1])], $manager->findAndInstallThemes());
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

        $this->cache
            ->shouldReceive('forever')
            ->once()
            ->with('installedThemes', null);

        $manager = new Manager($filesystem, $this->getTemplateRepository(), $this->cache);
        $this->assertEquals($themes, $manager->findAndInstallThemes());
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), $this->cache);
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), $this->cache);
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

        $manager = new Manager($filesystem, $this->getTemplateRepository(), $this->cache);
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

        $manager = new Manager($this->getFilesystem(), $provider, $this->cache);
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

        $manager = new Manager($this->getFilesystem(), $provider, $this->cache);
        $this->assertFalse($manager->templateIsInstalled($theme, $filename));
    }

    public function testGetInstalledThemesReturnsFromCache()
    {
        $themes = [];

        $this->cache
            ->shouldReceive('get')
            ->once()
            ->with('installedThemes')
            ->andReturn($themes);

        $manager = m::mock(Manager::class, [
            $this->getFilesystem(),
            $this->getTemplateRepository(),
            $this->cache,
        ])->makePartial();

        $manager
            ->shouldReceive('findAndInstallThemes')
            ->never();

        $this->assertEquals($themes, $manager->getInstalledThemes());
    }

    public function testGetInstalledThemesCreatesCache()
    {
        $themes = [];

        $this->cache
            ->shouldReceive('get')
            ->once()
            ->with('installedThemes')
            ->andReturn(null);

        $manager = m::mock(Manager::class, [
            $this->getFilesystem(),
            $this->getTemplateRepository(),
            $this->cache,
        ])->makePartial();

        $manager
            ->shouldReceive('findAndInstallThemes')
            ->once()
            ->andReturn($themes);

        $this->AssertEquals($themes, $manager->getInstalledThemes());
    }
}
