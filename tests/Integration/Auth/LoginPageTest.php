<?php

namespace BoomCMS\Tests\Integration\Auth;

use BoomCMS\Database\Models\Person;
use BoomCMS\Tests\AbstractTestCase;

class LoginPageTest extends AbstractTestCase
{
    public function testLoginPageShouldRedirectAuthenticatedUser()
    {
        $this->actingAs(new Person());

        $response = $this->call('GET', route('login'));

        $this->assertResponseStatus(302, $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    public function testLoginShouldLoadForGuest()
    {
        $response = $this->visitRoute('login');

        $this->assertResponseStatus(200, $response);
    }
}
