<?php

namespace BoomCMS\Tests\Console\Commands;

use BoomCMS\Console\Commands\InstallTemplates;
use BoomCMS\Core\Template\Manager;
use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class InstallTemplatesTest extends AbstractTestCase
{
    /**
     * @var Manager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $repository = m::mock(TemplateRepository::class);
        $filesystem = m::mock(Filesystem::class);
        $cache = m::mock(Cache::class)->makePartial();

        $this->manager = m::mock(Manager::class, [$filesystem, $repository, $cache]);
    }

    public function testTemplatesAreInstalledFeedback()
    {
        $this->manager
            ->shouldReceive('findAndInstallNewTemplates')
            ->once()
            ->andReturn([['testTheme', 'testTemplate']]);

        $command = $this->getCommand();

        $command->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Installed testTemplate in theme testTheme'));

        $command->fire($this->manager);
    }

    public function testNoTemplatesInstalledNotification()
    {
        $this->manager
            ->shouldReceive('findAndInstallNewTemplates')
            ->once()
            ->andReturn([]);

        $command = $this->getCommand();

        $command->expects($this->once())
            ->method('info')
            ->with($this->equalTo('No templates to install'));

        $command->fire($this->manager);
    }

    protected function getCommand()
    {
        return $this->createMock(InstallTemplates::class, ['info', 'call']);
    }
}
