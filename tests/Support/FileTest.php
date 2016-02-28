<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Support\File;
use BoomCMS\Tests\AbstractTestCase;

class FileTest extends AbstractTestCase
{
    public function testExtension()
    {
        $expected = [
            'csv' => ['test.csv', 'text/plain'],
            'txt' => ['test', 'text/plain'],
            'jpg' => ['test', 'image/jpeg'],
            'png' => ['test', 'image/png'],
        ];

        foreach ($expected as $extension => $args) {
            $this->assertEquals($extension, File::extension($args[0], $args[1]));
        }
    }

    public function testExtensionIsCaseInsensitive()
    {
        $this->assertEquals('txt', File::extension('test.TXT', ''));
    }

    public function testExtensionFromMimetype()
    {
        $config = include __DIR__.'/../../src/config/boomcms/assets.php';
        $extensions = $config['assets']['extensions'];

        foreach ($extensions as $extension => $mimetype) {
            $this->assertEquals($extension, File::extensionFromMimetype($mimetype));
        }
    }
}
