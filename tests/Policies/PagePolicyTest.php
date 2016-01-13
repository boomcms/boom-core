<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Policies\PagePolicy;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Auth;
use Mockery as m;

class PagePolicyTest extends AbstractTestCase
{
    public function testBeforeReturnsFalseIsUserCantAccessCurrentSite()
    {
        $site = new Site();
        Router::ShouldReceive('getActiveSite')->andReturn($site);

        $person = m::mock(Person::class)->makePartial();
        $person
            ->shouldReceive('hasSite')
            ->once()
            ->with($site)
            ->andReturn(false);

        $policy = new PagePolicy();
        $this->assertFalse($policy->before($person, ''));
    }

    public function testBeforeReturnsTrueForSuperUser()
    {
        $super = new Person([Person::ATTR_SUPERUSER => true]);
        $policy = new PagePolicy();

        $this->assertTrue($policy->before($super, ''));
    }

    public function testBeforeReturnsTrueIfTheyCanManagePages()
    {
        $site = new Site();
        Router::shouldReceive('getActiveSite')->andReturn($site);

        Auth::shouldReceive('check')->once()->with('managePages', $site)->andReturn(true);
        Auth::shouldReceive('check')->once()->with('managePages', $site)->andReturn(false);

        $person = m::mock(Person::class);
        $person->shouldReceive('hasSite')->andReturn(true);
        $person->shouldReceive('isSuperuser')->andReturn(false);
        $policy = new PagePolicy();

        $this->assertTrue($policy->before($person, ''));
        $this->assertNull($policy->before($person, ''));
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
