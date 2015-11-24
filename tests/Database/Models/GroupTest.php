<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Group;

class GroupTest extends AbstractModelTestCase
{
    protected $model = Group::class;

    public function testGetNameReturnsName()
    {
        $group = new Group([Group::ATTR_NAME => 'test']);

        $this->assertEquals('test', $group->getName());
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
