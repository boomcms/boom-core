<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\AssetVersion as Version;

class AssetVersionTest extends AbstractModelTestCase
{
    protected $model = Version::class;

    public function testGetAssetId()
    {
        $v = new Version([Version::ATTR_ASSET => 1]);

        $this->assertEquals($v->{Version::ATTR_ASSET}, $v->getAssetId());
        $this->assertInternalType('int', $v->getAssetId());
    }

    public function testExtensionIsAlwaysLowercase()
    {
        $v = new Version();
        $v->extension = 'TXT';

        $this->assertEquals('txt', $v->extension);
    }

    public function testExtensionIsOnlyAlphaNumeric()
    {
        $v = new Version();
        $v->extension = '. mp3 ';

        $this->assertEquals('mp3', $v->extension);
    }

    public function testFilenameCannotContainSlashes()
    {
        $filenames = ["test file\'s filename", 'file / name'];

        foreach ($filenames as $filename) {
            $v = new Version([Version::ATTR_FILENAME => $filename]);

            $this->assertEquals(str_replace(['/', '\\'], '', $filename), $v->{Version::ATTR_FILENAME});
        }
    }
}
