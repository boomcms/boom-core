<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Policies\PagePolicy;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Mockery as m;

class PagePolicyTest extends AbstractTestCase
{
    public function testBeforeReturnsTrueForSuperUser()
    {
        $super = new Person([Person::ATTR_SUPERUSER => true]);
        $normal = new Person([Person::ATTR_SUPERUSER => false]);
        $policy = new PagePolicy();

        $this->assertTrue($policy->before($super, ''));
        $this->assertNull($policy->before($normal, ''));
    }

    public function testBeforeReturnsTrueIfTheyCanManagePages()
    {
        Auth::shouldReceive('check')->once()->with('managePages', Request::instance())->andReturn(true);
        Auth::shouldReceive('check')->once()->with('managePages', Request::instance())->andReturn(false);

        $policy = new PagePolicy();

        $this->assertTrue($policy->before(new Person(), ''));
        $this->assertNull($policy->before(new Person(), ''));
    }

    public function testCertainRolesCanBePerformedIfUserCreatedPage()
    {
        $person = new Person();
        $page = m::mock(Page::class);
        $page
            ->shouldReceive('wasCreatedBy')
            ->times(3)
            ->with($person)
            ->andReturn(true);

        $policy = new PagePolicy();

        foreach (['edit', 'editContent', 'delete'] as $role) {
            $this->assertTrue($policy->$role($person, $page));
        }
    }
}
