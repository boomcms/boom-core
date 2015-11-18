<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Group;
use BoomCMS\Tests\AbstractTestCase;

class GroupTest extends AbstractTestCase
{
    public function testGetNameReturnsName()
    {
        $group = new Group([Group::ATTR_NAME => 'test']);

        $this->assertEquals('test', $group->getName());
    }

    public function testGetIdReturnsIdAttribute()
    {
        $group = new Group();
        $group->{Group::ATTR_ID} = 1;

        $this->assertEquals(1, $group->getId());
    }

    public function testSetName()
    {
        $group = new Group();
        $group->setName('test');

        $this->assertEquals('test', $group->getName());
    }

    public function testNameIsTrimmed()
    {
        $group = new Group();
        $group->setName(' test ');

        $this->assertEquals('test', $group->getName());
    }
}
