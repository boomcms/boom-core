<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Support\Str;
use BoomCMS\Tests\AbstractTestCase;

class StrTest extends AbstractTestCase
{
    protected $baseUrl = 'localhost';

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
}