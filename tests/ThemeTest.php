<?php

namespace BoomCMS\Tests;

use BoomCMS\Theme\Theme;

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
}
