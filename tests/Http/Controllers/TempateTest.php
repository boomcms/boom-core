<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Template;
use BoomCMS\Http\Controllers\TemplateController as Controller;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use Illuminate\Http\Request;
use Mockery as m;

class TempateTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testUpdate()
    {
        $name = 'test-name';
        $filename = 'test-filename';
        $description = 'test-description';

        $request = new Request([
            'name'        => $name,
            'description' => $description,
            'filename'    => $filename,
        ]);

        $template = m::mock(Template::class);

        $template
            ->shouldReceive('setName')
            ->once()
            ->with($name)
            ->andReturnSelf();

        $template
            ->shouldReceive('setDescription')
            ->once()
            ->with($description)
            ->andReturnSelf();

        $template
            ->shouldReceive('setFilename')
            ->once()
            ->with($filename)
            ->andReturnSelf();

        TemplateFacade::shouldReceive('save')
            ->once()
            ->with($template);

        $this->controller->update($request, $template);
    }

    public function testDestroy()
    {
        $template = new Template();

        TemplateFacade::shouldReceive('delete')
            ->once()
            ->with($template);

        $this->controller->destroy($template);
    }
}
