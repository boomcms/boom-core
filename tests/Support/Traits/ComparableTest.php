<?php

namespace BoomCMS\Tests\Support\Traits;

use BoomCMS\Support\Traits\Comparable as ComparableTrait;
use BoomCMS\Tests\AbstractTestCase;

class Comparable
{
    use ComparableTrait;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}

class ComparableType2 extends Comparable {}

class ComparableTest extends AbstractTestCase
{
    public function testIsReturnsFalseForInvalidPages()
    {
        $obj1 = new Comparable();
        $obj2 = new Comparable();

        $this->assertFalse($obj1->is($obj2));
    }

    public function testIsReturnsFalseIfArgIsNotAnObject()
    {
        $obj1 = new Comparable();

        $this->assertFalse($obj1->is('test'));
    }

    public function testIsReturnsFalseIfObjectsNotSameType()
    {
        $obj1 = new Comparable();
        $obj2 = new ComparableType2();

        $this->assertFalse($obj1->is($obj2));
    }

    public function testIsReturnsFalseForUnequalPages()
    {
        $obj1 = new Comparable(1);
        $obj2 = new Comparable(2);

        $this->assertFalse($obj1->is($obj2));
    }

    public function testIsReturnsWhetherPagesAreTheSame()
    {
        $obj1 = new Comparable(1);
        $obj2 = new Comparable(1);

        $this->assertTrue($obj1->is($obj2));
    }
}