<?php

namespace BoomCMS\Tests\Http\Middleware;

use BoomCMS\Http\Middleware\CheckPageAcl;
use BoomCMS\Routing\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Mockery as m;

class CheckPageAclTest extends AbstractTestCase
{
    /**
     * @var Gate
     */
    protected $gate;

    /**
     * @var Router
     */
    protected $router;

    public function setUp()
    {
        parent::setUp();

        $this->gate = m::mock(Gate::class);
        $this->router = m::mock(Router::class);
        $this->page = $this->validPage();

        $this->router
            ->shouldReceive('getActivePage')
            ->andReturn($this->page);

        $this->middleware = new CheckPageAcl($this->router, $this->gate);
    }

    public function testPageCannotBeViewed()
    {
        $this->gate
            ->shouldReceive('denies')
            ->with('view', $this->page)
            ->andReturn(true);

        $this->middleware->handle(new Request(), function () {
        });
    }

    public function testPageCanBeViewed()
    {
        $nextCalled = false;

        $this->gate
            ->shouldReceive('denies')
            ->with('view', $this->page)
            ->andReturn(false);

        $closure = function () use (&$nextCalled) {
            $nextCalled = true;
        };

        $this->middleware->handle(new Request(), $closure);

        $this->assertTrue($nextCalled);
    }
}
