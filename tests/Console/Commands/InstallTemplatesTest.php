<?php

namespace BoomCMS\Tests\Console\Commands;

use BoomCMS\Console\Commands\InstallTemplates;
use BoomCMS\Core\Template\Manager;
use BoomCMS\Tests\AbstractTestCase;

class InstallTemplatesTest extends AbstractTestCase
{
    public function testTemplatesAreInstalledFeedback()
    {
        $manager = $this->getManager();

        $manager->expects($this->once())
            ->method('findAndInstallNewTemplates')
            ->will($this->returnValue([['testTheme', 'testTemplate']]));

        $command = $this->getCommand();

        $command->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Installed testTemplate in theme testTheme'));

        $command->expects($this->once())
            ->method('call')
            ->with($this->equalTo('vendor:publish'));

        $command->fire($manager);
    }

    public function testNoTemplatesInstalledNotification()
    {
        $manager = $this->getManager();

        $manager->expects($this->once())
            ->method('findAndInstallNewTemplates')
            ->will($this->returnValue([]));

        $command = $this->getCommand();

        $command->expects($this->once())
            ->method('info')
            ->with($this->equalTo('No templates to install'));

        $command->fire($manager);
    }

    protected function getCommand()
    {
        return $this->getMock(InstallTemplates::class, ['info', 'call']);
    }

    protected function getManager()
    {
        return $this->getMockBuilder(Manager::class)
            ->setMethods(['findAndInstallNewTemplates'])
            ->disableOriginalConstructor()
            ->getMock();
    }
}
