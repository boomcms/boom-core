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

    public function testMakeInternalLinksRelative()
    {        
        $replacements = [
            "<a href='https://localhost/test'>test</a>" => "<a href='/test'>test</a>",
            "<a href='http://localhost/test'>test</a>" => "<a href='/test'>test</a>",
            "<a href=\"https://localhost/test\">test</a>" => "<a href=\"/test\">test</a>",
            "<a href=\"http://localhost/test\">test</a>" => "<a href=\"/test\">test</a>",
            "<a href='https://localhost/test'>https://localhost/test</a>" => "<a href='/test'>https://localhost/test</a>",
            "localhost/test" => "localhost/test",
            "<a href='https://www.localhost.com/test'></a>" => "<a href='https://www.localhost.com/test'></a>",
            "<a href='https://localhost/test?query=test#test'>test</a>" => "<a href='/test?query=test#test'>test</a>",
        ];

        foreach ($replacements as $original => $after) {
            $this->AssertEquals($after, Str::makeInternalLinksRelative($original));
        }
    }

    public function testOembed()
    {
        $replacements = [
            'https://www.youtube.com/watch?v=5omxorBHiv8' => '<iframe width="459" height="344" src="https://www.youtube.com/embed/5omxorBHiv8?feature=oembed" frameborder="0" allowfullscreen></iframe>',
            'https://youtu.be/5omxorBHiv8' => '<iframe width="459" height="344" src="https://www.youtube.com/embed/5omxorBHiv8?feature=oembed" frameborder="0" allowfullscreen></iframe>',
            '<a href="https://youtu.be/5omxorBHiv8">https://youtu.be/5omxorBHiv8</a>' => '<a href="https://youtu.be/5omxorBHiv8">https://youtu.be/5omxorBHiv8</a>',
        ];

        foreach ($replacements as $original => $after) {
            $this->AssertEquals($after, Str::OEmbed($original));
        }
    }
}