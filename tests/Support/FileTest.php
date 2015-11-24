<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Support\File;
use BoomCMS\Tests\AbstractTestCase;

class FileTest extends AbstractTestCase
{
    public function testMimeReturnsMimetypeOfFile()
    {
        $files = [
            'test.png' => 'image/png',
            'test.jpg' => 'image/jpeg',
            'test.txt' => 'text/plain',
        ];

        foreach ($files as $file => $mime) {
            $path = realpath(__DIR__.'/../files/'.$file);

            $this->assertEquals($mime, File::mime($path));
        }
    }

    public function testMimeWithInvalidPathReturnsFalse()
    {
        $this->assertFalse(File::mime(realpath(__DIR__.'/files/idonotexist')));
    }

    public function testMimeWithDirectoryReturnsFalse()
    {
        $this->assertFalse(File::mime(realpath(__DIR__)));
    }
}
