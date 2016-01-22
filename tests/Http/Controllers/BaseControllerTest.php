<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

abstract class BaseControllerTest extends AbstractTestCase
{
    protected $controller;
    
    /**
     * @var string
     */
    protected $className = '';

    public function setUp()
    {
        parent::setUp();

        if ($this->className) {
            $this->controller = m::mock($this->className)->makePartial();
        }
    }

    /**
     * @param string $role
     * @param Page $page
     */
    protected function requireRole($role, Page $page = null)
    {
        $this->controller
            ->shouldReceive('authorize')
            ->once()
            ->with($role, $page);
    }
}