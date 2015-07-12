<?php

use BoomCMS\Core\Models\Tag;

class Models_TagTest extends TestCase
{
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
}