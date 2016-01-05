<?php

namespace BoomCMS\Tests;

use Illuminate\Support\Facades\URL;

class RoutesTest extends AbstractTestCase
{
    public function testLoginRoute()
    {
        $this->assertEquals('http://localhost/boomcms/login', URL::route('login'));
    }

    public function testPasswordRoute()
    {
        $this->assertEquals('http://localhost/boomcms/recover', URL::route('password'));
    }
}
