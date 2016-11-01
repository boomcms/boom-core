<?php

namespace BoomCMS\Tests\Http\Middleware;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Http\Middleware\CheckPageAcl;
use BoomCMS\Routing\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery as m;

class CheckPageAclTest extends AbstractTestCase
{
    /**
     * @var Gate
     */
    protected $gate;

    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        parent::setUp();

        $this->gate = m::mock(Gate::class);
        $this->guard = m::mock(Guard::class);
        $this->router = m::mock(Router::class);
        $this->page = $this->validPage();

        $this->router
            ->shouldReceive('getActivePage')
            ->andReturn($this->page);

        $this->page->setAclEnabled(true);

        $this->middleware = new CheckPageAcl($this->router, $this->guard, $this->gate);
    }

    public function testRedirectToLoginIfGuest()
    {
        $this->guard
            ->shouldReceive('check')
            ->andReturn(false);

        $response = $this->middleware->handle(new Request(), function () {
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->status());
        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testLoggedInButPageCannotBeViewed()
    {
        $this->guard
            ->shouldReceive('check')
            ->andReturn(true);

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

        $this->guard
            ->shouldReceive('check')
            ->andReturn(true);

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

    public function testAclDisabled()
    {
        $this->page->setAclEnabled(false);
        $nextCalled = false;

        $this->guard
            ->shouldReceive('check')
            ->never();

        $this->gate
            ->shouldReceive('denies')
            ->with('view', $this->page)
            ->never();

        $closure = function () use (&$nextCalled) {
            $nextCalled = true;
        };

        $this->middleware->handle(new Request(), $closure);

        $this->assertTrue($nextCalled);
    }
}
