<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Template;
use BoomCMS\Repositories\Template as TemplateRepository;

class TemplateTest extends BaseRepositoryTest
{
    protected $modelClass = Template::class;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new TemplateRepository($this->model);
    }

    public function testCreate()
    {
        $attrs = [
            'filename' => 'test',
            'name'     => 'test',
        ];

        $this->model->shouldReceive('create')->with($attrs);
        $this->repository->create($attrs);
    }
}
