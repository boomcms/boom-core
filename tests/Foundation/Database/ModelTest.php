<?php

namespace BoomCMS\Tests\Foundation\Database;

use BoomCMS\Foundation\Database\Model;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class ModelTest extends AbstractTestCase
{
    public function testIsReturnsFalseForInvalidModels()
    {
        $obj1 = m::mock(Model::class)->makePartial();

        $this->assertFalse($obj1->is($obj1));
    }

    public function testIsReturnsFalseIfObjectsNotSameType()
    {
        $obj1 = m::mock(Model::class)->makePartial();
        $obj2 = m::mock(Model::class)->makePartial();

        $this->assertFalse($obj1->is($obj2));
    }

    public function testIsReturnsFalseForUnequalModels()
    {
        $obj1 = m::mock(Model::class)->makePartial();
        $obj1->{Model::ATTR_ID} = 1;

        $obj2 = m::mock(Model::class)->makePartial();
        $obj2->{Model::ATTR_ID} = 2;

        $this->assertFalse($obj1->is($obj2));
    }

    public function testIsReturnsWhetherModelsAreTheSame()
    {
        $obj1 = m::mock(Model::class)->makePartial();
        $obj1->{Model::ATTR_ID} = 1;

        $obj2 = m::mock(Model::class)->makePartial();
        $obj2->{Model::ATTR_ID} = 1;

        $this->assertTrue($obj1->is($obj2));
    }
}
