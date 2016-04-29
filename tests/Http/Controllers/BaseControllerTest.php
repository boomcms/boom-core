<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Page;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;
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
            $this->controller = m::mock($this->className)
                ->makePartial()
                ->shouldAllowMockingProtectedMethods();
        }
    }

    protected function login()
    {
        $person = m::mock(Person::class)->makePartial();
        $person->shouldReceive('save');

        Auth::login($person);
    }

    /**
     * @param string $role
     * @param Page   $page
     */
    protected function requireRole($role, Page $page = null)
    {
        $this->login();

        $this->controller
            ->shouldReceive('authorize')
            ->once()
            ->with($role, $page);
    }
}
