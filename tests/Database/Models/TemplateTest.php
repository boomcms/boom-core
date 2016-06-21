<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Template;
use BoomCMS\Theme\Theme;
use Illuminate\Support\Facades\View;
use Mockery as m;

class TemplateTest extends AbstractModelTestCase
{
    protected $model = Template::class;

    public function testAjaxFormIncludesFileExists()
    {
        $template = m::mock(Template::class)->makePartial();
        $template->shouldReceive('fileExists')->once()->andReturn(true);

        $json = $template->toJson();
        $data = json_decode($json);

        $this->assertEquals(1, $data->file_exists);
    }

    public function testGetThemeName()
    {
        $template = new Template(['theme' => 'test']);

        $this->assertEquals('test', $template->getThemeName());
    }

    public function testGetTheme()
    {
        $template = new Template(['theme' => 'test']);

        $this->assertInstanceOf(Theme::class, $template->getTheme());
    }

    public function testGetFullFilename()
    {
        $filename = 'test::templates.test';
        $template = new Template(['theme' => 'test', 'filename' => 'test']);

        $this->assertEquals($filename, $template->getFullFilename());
    }

    public function testFileExists()
    {
        $template = new Template(['theme' => 'test', 'filename' => 'test']);

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

    public function testGetViewName()
    {
        $template = new Template(['theme' => 'test', 'filename' => 'test']);

        $this->assertEquals('test:templates.test', $template->getViewName());
    }

    public function testGetView()
    {
        $template = new Template(['theme' => 'test', 'filename' => 'test']);

        View::shouldReceive('exists')->with($template->getFullFilename())->andReturn(true);
        View::shouldReceive('make')->with($template->getFullFilename());

        $template->getView();
    }

    public function testGetViewReturnsDefaultViewWhenViewDoesntExist()
    {
        $template = new Template(['theme' => 'test', 'filename' => 'test']);

        View::shouldReceive('exists')->with($template->getFullFilename())->andReturn(false);
        View::shouldReceive('make')->with('boomcms::templates.default');

        $template->getView();
    }

    public function testGetViewReturnsDefaultViewWithNoView()
    {
        $template = new Template();

        View::shouldReceive('exists')->with(\Mockery::any())->andReturn(false);
        View::shouldReceive('make')->with('boomcms::templates.default');

        $template->getView();
    }

    public function testSetDescription()
    {
        $desc = 'test';
        $template = new Template();
        $template->setDescription($desc);

        $this->assertEquals($desc, $template->getDescription());
    }

    public function testSetFilename()
    {
        $filename = 'test';
        $template = new Template();
        $template->setFilename($filename);

        $this->assertEquals($filename, $template->getFilename());
    }

    public function testSetName()
    {
        $name = 'test';
        $template = new Template();
        $template->setName($name);

        $this->assertEquals($name, $template->getName());
    }
}
