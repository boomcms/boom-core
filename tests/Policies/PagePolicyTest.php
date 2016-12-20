<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Policies\PagePolicy;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Gate;
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

    public function testCanAddDeleteEditAndViewIfTheyCanManagePages()
    {
        $site = new Site();
        Router::shouldReceive('getActiveSite')->andReturn($site);

        Gate::shouldReceive('allows')->times(4)->with('managePages', $site)->andReturn(true);

        $page = m::mock(Page::class);
        $page->shouldReceive('aclEnabled')->once()->andReturn(true);
        $page->shouldReceive('wasCreatedBy')->andReturn(false);

        $person = new Person();
        $policy = new PagePolicy();

        $this->assertTrue($policy->add($person, $page));
        $this->assertTrue($policy->delete($person, $page));
        $this->assertTrue($policy->edit($person, $page));
        $this->assertTrue($policy->view($person, $page));
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

        $page
            ->shouldReceive('aclEnabled')
            ->once()
            ->andReturn(true);

        $policy = new PagePolicy();

        foreach (['edit', 'delete', 'view'] as $role) {
            $this->assertTrue($policy->$role($person, $page));
        }
    }

    public function testViewReturnsTrueIfPageHasNoAclGroups()
    {
        $person = m::mock(Person::class);

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('getAclGroupIds')
            ->once()
            ->andReturn([]);

        $page
            ->shouldReceive('aclEnabled')
            ->once()
            ->andReturn(true);

        $page
            ->shouldReceive('wasCreatedBy')
            ->once()
            ->with($person)
            ->andReturn(false);

        $policy = new PagePolicy();

        $this->assertTrue($policy->view($person, $page));
    }

    public function testViewReturnsWhetherUserIsInGroupsWhichCanViewPage()
    {
        $groups = [new Group(), new Group()];
        $groups[0]->id = 1;
        $groups[1]->id = 2;

        $person = m::mock(Person::class);
        $person
            ->shouldReceive('getGroups')
            ->times(2)
            ->andReturn($groups);

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('getAclGroupIds')
            ->once()
            ->andReturn([1]);

        $page
            ->shouldReceive('getAclGroupIds')
            ->once()
            ->andReturn([3]);

        $page
            ->shouldReceive('aclEnabled')
            ->times(2)
            ->andReturn(true);

        $page
            ->shouldReceive('wasCreatedBy')
            ->times(2)
            ->with($person)
            ->andReturn(false);

        $policy = new PagePolicy();

        $this->assertTrue($policy->view($person, $page));
        $this->assertFalse($policy->view($person, $page));
    }
}
