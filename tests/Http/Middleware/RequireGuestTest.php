<?php

namespace BoomCMS\Tests\Http\Middleware;

use BoomCMS\Http\Middleware\RequireGuest;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery as m;

class RequireGuestTest extends AbstractTestCase
{
    public function testGuestIsNotRedirected()
    {
        $nextCalled = false;

        $auth = m::mock(Auth::class);
        $auth->shouldReceive('check')->once()->andReturn(false);

        $closure = function () use (&$nextCalled) {
            $nextCalled = true;
        };

        $middleware = new RequireGuest($auth);
        $middleware->handle(new Request(), $closure);

        $this->assertTrue($nextCalled);
    }

    public function testUserIsRedirected()
    {
        $nextCalled = false;

        $auth = m::mock(Auth::class);
        $auth->shouldReceive('check')->once()->andReturn(true);

        $closure = function () use (&$nextCalled) {
            $nextCalled = true;
        };

        $middleware = new RequireGuest($auth);
        $response = $middleware->handle(new Request(), $closure);

        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->status());
        $this->assertEquals('/', $response->getTargetUrl());
    }
}
