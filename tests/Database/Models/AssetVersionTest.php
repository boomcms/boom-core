<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\AssetVersion as V;

class AssetVersionTest extends AbstractModelTestCase
{
    protected $model = V::class;

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
