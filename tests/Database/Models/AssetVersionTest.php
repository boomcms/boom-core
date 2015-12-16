<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\AssetVersion as V;

class AssetVersionTest extends AbstractModelTestCase
{
    protected $model = V::class;

    public function testGetAssetId()
    {
        $v = new V([V::ATTR_ASSET => 1]);

        $this->assertEquals($v->{V::ATTR_ASSET}, $v->getAssetId());
        $this->assertInternalType('int', $v->getAssetId());
    }

    public function testExtensionIsAlwaysLowercase()
    {
        $v = new V();
        $v->extension = 'TXT';

        $this->assertEquals('txt', $v->extension);
    }

    public function testExtensionIsOnlyAlphaNumeric()
    {
        $v = new V();
        $v->extension = '. mp3 ';

        $this->assertEquals('mp3', $v->extension);
    }
}
