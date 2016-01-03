<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Policies\SitePolicy;
use BoomCMS\Database\Models\Person;
use BoomCMS\Tests\AbstractTestCase;

class SitePolicyTest extends AbstractTestCase
{
    public function testBeforeReturnsTrueForSuperUser()
    {
        $super = new Person([Person::ATTR_SUPERUSER => true]);
        $normal = new Person([Person::ATTR_SUPERUSER => false]);
        $policy = new SitePolicy();

        $this->assertTrue($policy->before($super, ''));
        $this->assertNull($policy->before($normal, ''));
    }
}