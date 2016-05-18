<?php

namespace BoomCMS\Tests\Policies;

use BoomCMS\Database\Models\Person;
use BoomCMS\Policies\PersonPolicy;
use BoomCMS\Tests\AbstractTestCase;

class PersonPolicyTest extends AbstractTestCase
{
    public function testCannotEditSuperUserIfNotAlsoASuperuser()
    {
        $user = new Person();
        $editing = new Person();
        $policy = new PersonPolicy();

        $this->assertFalse($policy->editSuperuser($user, $editing));
    }

    public function testCannotEditSuperUserOfThemselves()
    {
        $user = new Person([Person::ATTR_SUPERUSER => true]);
        $user->{Person::ATTR_ID} = 1;

        $policy = new PersonPolicy();

        $this->assertFalse($policy->editSuperuser($user, $user));
    }

    public function testSuperuserCanEditSuperuserPropertyOfOthers()
    {
        $user = new Person([Person::ATTR_SUPERUSER => true]);
        $user->{Person::ATTR_ID} = 1;

        $editing = new Person();
        $editing->{Person::ATTR_ID} = 2;
        $policy = new PersonPolicy();

        $this->assertTrue($policy->editSuperuser($user, $editing));
    }
}
