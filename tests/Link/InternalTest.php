<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Tests\AbstractTestCase;

abstract class InternalTest extends AbstractTestCase
{
    public function testGetFeatureImageIdReturnsAssetIdAttribute()
    {
        $assetId = 1;
        $link = new $this->linkClass(new $this->objectClass(), ['asset_id' => $assetId]);

        $this->assertEquals($assetId, $link->getFeatureImageId());
    }

    public function testGetTitleReturnsTitleAttribute()
    {
        $title = 'link title';
        $link = new $this->linkClass(new $this->objectClass(), ['title' => $title]);

        $this->assertEquals($title, $link->getTitle());
    }

    public function testGetTextReturnsTextAttribute()
    {
        $text = 'link text';
        $link = new $this->linkClass(new $this->objectClass(), ['text' => $text]);

        $this->assertEquals($text, $link->getText());
    }

    public function testIsInternalReturnsTrue()
    {
        $link = new $this->linkClass(new $this->objectClass());

        $this->assertTrue($link->isInternal());
    }

    public function testIsExternalReturnsFalse()
    {
        $link = new $this->linkClass(new $this->objectClass());

        $this->assertFalse($link->isExternal());
    }
}
