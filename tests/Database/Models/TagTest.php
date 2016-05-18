<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Collection\TagCollection;
use BoomCMS\Database\Models\Tag;

class TagTest extends AbstractModelTestCase
{
    protected $model = Tag::class;

    public function testNameIsTrimmed()
    {
        $tag = new Tag();
        $tag->name = ' test ';

        $this->assertEquals('test', $tag->name);
    }

    public function testHtmlIsRemovedFromName()
    {
        $tag = new Tag();
        $tag->name = '<p>test</p>';

        $this->assertEquals('test', $tag->name);
    }

    public function testSlugIsDefinedAutomatically()
    {
        $tag = new Tag();
        $tag->name = 'Test tag name';

        $this->assertEquals($tag->slug, 'test-tag-name');
    }

    public function testSlugIsCreatedAfterNameIsCleaned()
    {
        $tag = new Tag();
        $tag->name = ' <p>test</p><br />';

        $this->assertEquals('test', $tag->slug);
    }

    public function testGetSlug()
    {
        $tag = new Tag();
        $this->assertNull($tag->getSlug());

        $tag = new Tag(['slug' => 'test']);
        $this->assertEquals('test', $tag->getSlug());
    }

    public function testGetName()
    {
        $tag = new Tag();
        $this->assertNull($tag->getName());

        $tag = new Tag(['name' => 'test']);
        $this->assertEquals('test', $tag->getName());
    }

    public function testGetGroup()
    {
        $tag = new Tag();
        $this->assertNull($tag->getGroup());

        $tag = new Tag(['group' => 'test']);
        $this->assertEquals('test', $tag->getGroup());
    }

	public function testNewCollectionReturnsTagCollection()
	{
		$tag = new Tag();

		$this->assertInstanceOf(TagCollection::class, $tag->newCollection());
	}

    public function testSetName()
    {
        $tag = new Tag();
        $tag->setName('test');

        $this->assertEquals('test', $tag->getName());
    }

    public function testSetNameUpdatesSlug()
    {
        $tag = new Tag();
        $tag->setName('Test test');

        $this->assertEquals('test-test', $tag->getSlug());
    }
}
