<?php

namespace BoomCMS\Tests\Theme;

use BoomCMS\Tests\AbstractTestCase;
use BoomCMS\Theme\Theme;
use Illuminate\Support\Facades\File;

class ThemeTest extends AbstractTestCase
{
    public function testGetName()
    {
        $theme = new Theme('test');

        $this->assertEquals('test', $theme->getName());
    }

    public function testGetThemesDirectory()
    {
        $theme = new Theme();
        $this->assertEquals(storage_path().'/boomcms/themes', $theme->getThemesDirectory());
    }

    public function testGetDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals(storage_path().'/boomcms/themes/test', $theme->getDirectory());
    }

    public function testGetConfigDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals(storage_path().'/boomcms/themes/test/src/config', $theme->getConfigDirectory());
    }

    public function testGetViewDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals(storage_path().'/boomcms/themes/test/src/views', $theme->getViewDirectory());
    }

    public function testGetTemplateDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals(storage_path().'/boomcms/themes/test/src/views/templates', $theme->getTemplateDirectory());
    }

    public function testGetPublicDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals(storage_path().'/boomcms/themes/test/public', $theme->getPublicDirectory());
    }

    public function testInitFileIsRequiredIfItExists()
    {
        $filename = storage_path('boomcms/themes/test/init.php');

        File::shouldReceive('exists')
            ->once()
            ->with($filename)
            ->andReturn(true);

        File::shouldReceive('requireOnce')
            ->once()
            ->with($filename);

        $theme = new Theme('test');

        $theme->init();
    }

    public function testInitFileIsNotRequiredIfItDoesntExist()
    {
        $filename = storage_path('boomcms/themes/test/init.php');

        File::shouldReceive('exists')
            ->once()
            ->with($filename)
            ->andReturn(false);

        File::shouldReceive('requireOnce')
            ->never()
            ->with($filename);

        $theme = new Theme('test');

        $theme->init();
    }
}
