<?php

namespace BoomCMS\Tests\Tag;

use BoomCMS\Core\Tag\Tag;
use BoomCMS\Tests\AbstractTestCase;

class TagTest extends AbstractTestCase
{
    public function testGetSlug()
    {
        $tag = new Tag([]);
        $this->assertNull($tag->getSlug());

        $tag = new Tag(['slug' => 'test']);
        $this->assertEquals('test', $tag->getSlug());
    }

    public function testGetName()
    {
        $tag = new Tag([]);
        $this->assertNull($tag->getName());

        $tag = new Tag(['name' => 'test']);
        $this->assertEquals('test', $tag->getName());
    }

    public function testGetGroup()
    {
        $tag = new Tag([]);
        $this->assertNull($tag->getGroup());

        $tag = new Tag(['group' => 'test']);
        $this->assertEquals('test', $tag->getGroup());
    }

    public function testLoaded()
    {
        $tag = new Tag([]);
        $this->assertFalse($tag->loaded());

        $tag = new Tag(['id' => 1]);
        $this->assertTrue($tag->loaded());
    }

    public function testSetName()
    {
        $tag = new Tag([]);
        $tag->setName('test');

        $this->assertEquals('test', $tag->getName());
    }

    public function testSetSlug()
    {
        $tag = new Tag([]);
        $tag->setSlug('test');

        $this->assertEquals('test', $tag->getSlug());
    }
}
