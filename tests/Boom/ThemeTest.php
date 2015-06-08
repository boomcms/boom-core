<?php

use BoomCMS\Core\Theme\Theme;

use Illuminate\Support\Facades\View;

class ThemeTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $theme = new Theme('test');

        $this->assertEquals('test', $theme->getName());
    }

    public function testGetThemesDirectory()
    {
        $theme = new Theme();
        $this->assertEquals('storage/boomcms/themes', $theme->getThemesDirectory());
    }

    public function testGetDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals('storage/boomcms/themes/test', $theme->getDirectory());
    }

    public function testGetConfigDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals('storage/boomcms/themes/test/src/config', $theme->getConfigDirectory());
    }

    public function testGetViewDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals('storage/boomcms/themes/test/src/views', $theme->getViewDirectory());
    }

    public function testGetTemplateDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals('storage/boomcms/themes/test/src/views/templates', $theme->getTemplateDirectory());
    }

    public function testGetPublicDirectory()
    {
        $theme = new Theme('test');
        $this->assertEquals('storage/boomcms/themes/test/public', $theme->getPublicDirectory());
    }
}