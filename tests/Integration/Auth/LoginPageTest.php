<?php

namespace BoomCMS\Tests\Integration\Auth;

use BoomCMS\Database\Models\Person;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

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

    public function testLoginWithValidDetails()
    {
        $this->withoutMiddleware();

        $credentials = [
            'email'    => 'test',
            'password' => 'test',
        ];

        $person = new Person();

        PersonFacade::shouldReceive('retrieveByCredentials')
            ->once()
            ->with($credentials)
            ->andReturn($person);

        Auth::provider('boomcms', function () {
            return PersonFacade::getFacadeRoot();
        });

        App::getFacadeRoot()['config']['auth.providers.users'] = ['provider' => 'boomcms'];

        $response = $this->call('POST', route('processLogin'), $credentials);
dd($response);
        $this->assertResponseStatus(200, $response);
    }
}
