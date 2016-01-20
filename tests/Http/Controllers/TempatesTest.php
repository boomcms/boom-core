<?php

namespace BoomCMS\Tests\Controllers;

use BoomCMS\Database\Models\Template;
use BoomCMS\Http\Controllers\Templates as Controller;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery as m;

class TemplatesTest extends AbstractTestCase
{
    /**
     * @var Controller
     */
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->controller = m::mock(Controller::class)->makePartial();
    }

    public function testSave()
    {
        $template1 = m::mock(Template::class)->makePartial();
        $template2 = m::mock(Template::class)->makePartial();
        $templates = [$template1, $template2];
        $templateIds = [1, 2];

        $template1->{Template::ATTR_ID} = 1;
        $template2->{Template::ATTR_ID} = 2;

        $templateData = [
            'templates'     => $templateIds,
            'name-1'        => 'name 1',
            'description-1' => 'description 1',
            'filename-1'    => 'filename 1',
            'name-2'        => 'name 2',
            'description-2' => 'description 2',
            'filename-2'    => 'filename 2'
        ];

        TemplateFacade::shouldReceive('find')
            ->once()
            ->with($templateIds)
            ->andReturn($templates);

        foreach ($templates as $i => $template) {
            $i++;

            $template
                ->shouldReceive('setName')
                ->once()
                ->with($templateData["name-$i"])
                ->andReturnSelf();

            $template
                ->shouldReceive('setDescription')
                ->once()
                ->with($templateData["description-$i"])
                ->andReturnSelf();

            $template
                ->shouldReceive('setFilename')
                ->once()
                ->with($templateData["filename-$i"])
                ->andReturnSelf();

            TemplateFacade::shouldReceive('save')
                ->once()
                ->with($template);
        }

        $request = new Request($templateData);
        $this->controller->save($request);
    }

    public function testDelete()
    {
        $template = new Template();

        TemplateFacade::shouldReceive('delete')
            ->once()
            ->with($template);

        $this->controller->delete($template);
    }
}