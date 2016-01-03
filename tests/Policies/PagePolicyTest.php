<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Policies\PagePolicy;
use BoomCMS\Database\Models\Person;
use BoomCMS\Tests\AbstractTestCase;

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

    public function beforeReturnsTrueIfTheyCanManagePages()
    {
        $this->markTestIncomplete();
    }

    public function testUserCanDeleteIfTheyCreatedPage()
    {
        $this->markTestIncomplete();
    }

    public function testUserCanEditTemplateIfTheyCreatedPage()
    {
        $this->markTestIncomplete();
    }

    public function testUserCanEditContentIfTheyCreatedPage()
    {
        $this->markTestIncomplete();
    }
}