<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Template;
use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class TemplateTest extends AbstractTestCase
{
    public function testDeleteById()
    {
        $model = m::mock(Template::class);
        $model->shouldReceive('destroy')->with(1);

        $repository = new TemplateRepository($model);
        $repository->deleteById(1);
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
