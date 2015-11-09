<?php

namespace BoomCMS\Tests\Support\Traits;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Core\Group\Group;
use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Page\Version;
use BoomCMS\Core\Person\Person;
use BoomCMS\Core\Tag\Tag;
use BoomCMS\Core\Template\Template;
use BoomCMS\Core\URL\URL;
use BoomCMS\Support\Traits\HasId as HasIdTrait;
use BoomCMS\Tests\AbstractTestCase;

class HasId
{
    use HasIdTrait;

    public function __construct($id = null)
    {
        $this->attributes['id'] = $id;
    }
}

class HasIdTest extends AbstractTestCase
{
    public function testGetId()
    {
        $obj = new HasId(1);

        $this->assertEquals(1, $obj->getId());
    }

    public function testGetIdAlwaysReturnsInt()
    {
        $obj = new HasId();

        $this->assertEquals(0, $obj->getId());
    }

    public function testIdIsSet()
    {
        $obj = new HasId();
        $obj->setId(1);

        $this->assertEquals(1, $obj->getId());
    }

    public function testIdCantBeChanged()
    {
        $obj = new HasId(1);
        $obj->setId(2);

        $this->assertEquals(1, $obj->getId());
    }

    public function testLoadedReturnsTrue()
    {
        $obj = new HasId(1);

        $this->assertTrue($obj->loaded());
    }

    public function testLoadedReturnsFalse()
    {
        $obj = new HasId();

        $this->assertFalse($obj->loaded());
    }

    public function testClassesUseTrait()
    {
        $classes = [
            Asset::class,
            Group::class,
            Page::class,
            Person::class,
            Tag::class,
            Template::class,
            URL::class,
            Version::class,
        ];

        foreach ($classes as $class) {
            $this->assertContains(HasIdTrait::class, class_uses($class), $class);
        }
    }
}
