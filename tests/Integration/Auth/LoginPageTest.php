<?php

namespace BoomCMS\Tests\Integration\Auth;

use BoomCMS\Database\Models\Person;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;

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

    public function testSuccessfulLogin()
    {
        $credentials = [
            'email'    => 'test@boomcms.net',
            'password' => 'test',
        ];

        $person = new Person();

        $this->people
            ->shouldReceive('retrieveByCredentials')
            ->once()
            ->with($credentials)
            ->andReturn($person);

        $this->people
            ->shouldReceive('validateCredentials')
            ->once()
            ->with($person, $credentials)
            ->andReturn(true);

        $response = $this->call('POST', route('login'), $credentials);

        $this->assertTrue(Auth::check());
        $this->assertEquals($person, Auth::user());

        return $response;
    }

    /**
     * @depends testSuccessfulLogin
     */
    public function testRediredctToDashboardAfterLogin($response)
    {
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('dashboard'), $response->getTargetUrl());
    }
}
