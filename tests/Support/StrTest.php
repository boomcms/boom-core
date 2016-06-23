<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Support\Str;
use BoomCMS\Tests\AbstractTestCase;

class StrTest extends AbstractTestCase
{
    public function testFilesize()
    {
        $sizes = [
            1000                => '1000B',
            1024                => '1KiB',
            1024 * 1024         => '1MiB',
            1126                => '1.1KiB',
            (1024 * 1024) * 1.1 => '1.1MiB',
        ];

        foreach ($sizes as $size => $readable) {
            $this->assertEquals($readable, Str::filesize($size));
        }
    }

    public function testMakeInternalLinksRelative()
    {
        $replacements = [
            "<a href='https://localhost/test'>test</a>"                   => "<a href='/test'>test</a>",
            "<a href='http://localhost/test'>test</a>"                    => "<a href='/test'>test</a>",
            '<a href="https://localhost/test">test</a>'                   => '<a href="/test">test</a>',
            '<a href="http://localhost/test">test</a>'                    => '<a href="/test">test</a>',
            "<a href='https://localhost/test'>https://localhost/test</a>" => "<a href='/test'>https://localhost/test</a>",
            'localhost/test'                                              => 'localhost/test',
            "<a href='https://www.localhost.com/test'></a>"               => "<a href='https://www.localhost.com/test'></a>",
            "<a href='https://localhost/test?query=test#test'>test</a>"   => "<a href='/test?query=test#test'>test</a>",
        ];

        foreach ($replacements as $original => $after) {
            $this->assertEquals($after, Str::makeInternalLinksRelative($original));
        }
    }

    public function testnl2paragraph()
    {
        $replacements = [
            'text'       => '<p>text</p>',
            "text\ntext" => '<p>text</p><p>text</p>',
        ];

        foreach ($replacements as $original => $after) {
            $this->assertEquals($after, Str::nl2paragraph($original));
        }
    }

    public function testUniqueWithAUniqueString()
    {
        $string = 'test';
        $result = Str::unique($string, function() {
            return true;
        });

        $this->assertEquals($string, $result);
    }

    public function testMakeUniqueAppendsIncrementalNumber()
    {
        $string = 'test';
        $unique = 'test2';
        $i = 0;

        $callback = function() use (&$i) {
            $i++;

            return $i > 2;
        };

        $this->assertEquals($unique, Str::unique($string, $callback));
    }
}
