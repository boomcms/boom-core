<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Template;
use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class TemplateTest extends AbstractTestCase
{
    public function testDelete()
    {
        $template = m::mock(Template::class);
        $template->shouldReceive('delete');

        $repository = new TemplateRepository(new Template());
        $this->assertEquals($repository, $repository->delete($template));
    }

    public function testFind()
    {
        $model = m::mock(Template::class);
        $model->shouldReceive('find')->with(1);

        $repository = new TemplateRepository($model);
        $repository->find(1);
    }

    public function testCreate()
    {
        $attrs = [
            'filename' => 'test',
            'name'     => 'test',
        ];

        $model = m::mock(Template::class);
        $model->shouldReceive('create')->with($attrs);

        $repository = new TemplateRepository($model);
        $repository->create($attrs);
    }
}
