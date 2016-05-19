<?php

namespace BoomCMS\Tests\Collection;

use BoomCMS\Collection\TagCollection;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Collection;

class TagCollectionTest extends AbstractTestCase
{
    public function testInheritsCollection()
    {
        $this->assertInstanceOf(Collection::class, new TagCollection());
    }

    public function testGetNames()
    {
        $tag1 = new Tag([Tag::ATTR_NAME => 'tag1']);
        $tag2 = new Tag([Tag::ATTR_NAME => 'tag2']);

        $tags = [$tag1, $tag2];
        $names = [$tag1->getName(), $tag2->getName()];

        $collection = new TagCollection($tags);

        $this->assertEquals($names, $collection->getNames());

        return $collection;
    }

    public function testGetNamesReturnsEmptyArray()
    {
        $collection = new TagCollection([]);

        $this->assertEquals([], $collection->getNames());
    }

    /**
     * @depends testGetNames
     */
    public function testStringIsCommaSeperatedList($collection)
    {
        $expect = implode(', ', $collection->getNames());

        $this->assertEquals($expect, (string) $collection);
    }
}
