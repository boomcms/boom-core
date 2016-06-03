<?php

namespace BoomCMS\Tests\Http\Middleware;

use BoomCMS\Http\Middleware\RequireLogin;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Mockery as m;

class RequireLoginTest extends AbstractTestCase
{
    public function testGuestIsRedirected()
    {
        $nextCalled = false;

        $auth = m::mock(Auth::class);
        $auth->shouldReceive('check')->once()->andReturn(false);

        $closure = function() use(&$nextCalled) {
            $nextCalled = true;
        };

        $middleware = new RequireLogin($auth);
        $response = $middleware->handle(new Request, $closure);

        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->status());
        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    public function testUserIsRedirected()
    {
        $nextCalled = false;

        $auth = m::mock(Auth::class);
        $auth->shouldReceive('check')->once()->andReturn(true);

        $closure = function() use(&$nextCalled) {
            $nextCalled = true;
        };

        $middleware = new RequireLogin($auth);
        $$middleware->handle(new Request, $closure);

        $this->assertTrue($nextCalled);
    }
}
