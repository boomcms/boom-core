<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Policies\SitePolicy;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class SitePolicyTest extends AbstractTestCase
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

        $policy = new SitePolicy();
        $this->assertFalse($policy->before($person, ''));
    }

    public function testBeforeReturnsTrueForSuperUser()
    {
        $super = new Person([Person::ATTR_SUPERUSER => true]);
        $policy = new SitePolicy();

        $this->assertTrue($policy->before($super, ''));
    }
}
