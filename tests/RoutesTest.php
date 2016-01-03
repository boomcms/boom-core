<?php

namespace BoomCMS\Tests;

use Illuminate\Support\Facades\URL;

class RoutesTest extends AbstractTestCase
{
    public function testLoginRoute()
    {
        $this->assertEquals('http://localhost/cms/login', URL::route('login'));
    }
}