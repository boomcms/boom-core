<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Support\Str;
use BoomCMS\Tests\AbstractTestCase;

class StrTest extends AbstractTestCase
{
    public function testFilesize()
    {
        $sizes = [
            1000              => '1000B',
            1024              => '1KiB',
            1024*1024         => '1MiB',
            1126              => '1.1KiB',
            (1024*1024) * 1.1 => '1.1MiB',
        ];

        foreach ($sizes as $size => $readable) {
            $this->assertEquals($readable, Str::filesize($size));
        }
    }
}