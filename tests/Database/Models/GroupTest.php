<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Site;
use Mockery as m;

class GroupTest extends AbstractModelTestCase
{
    protected $model = Group::class;

    public function testGetNameReturnsName()
    {
        $group = new Group([Group::ATTR_NAME => 'test']);

        $this->assertEquals('test', $group->getName());
    }

    public function testGetSite()
    {
        $site = new Site();
        $group = m::mock(Group::class.'[belongsTo,first]');

        $group
            ->shouldReceive('belongsTo')
            ->once()
            ->with(Site::class, 'site_id')
            ->andReturnSelf();

        $group
            ->shouldReceive('first')
            ->once()
            ->andReturn($site);

        $this->assertEquals($site, $group->getSite());
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
