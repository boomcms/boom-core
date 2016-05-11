<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Link\External as Link;
use BoomCMS\Tests\AbstractTestCase;

class ExternalTest extends AbstractTestCase
{
    public function testUrlReturnsURLUnchanged()
    {
        $links = [
            'http://www.google.com/test',
            'www.google.com/test',
            'www.google.com/test?test=test#test',
        ];

        foreach ($links as $l) {
            $link = new Link($l);

            $this->assertEquals($l, $link->url());
        }
    }

    public function testGetTitleReturnsTheUrl()
    {
        $links = [
            'http://www.google.com/test',
            'www.google.com/test',
            'www.google.com/test?test=test#test',
        ];

        foreach ($links as $l) {
            $link = new Link($l);

            $this->assertEquals($l, $link->getTitle());
        }
    }

    public function testGetHostnameReturnsTheHostname()
    {
        $url = 'http://www.boomcms.net/hello';
        $link = new Link($url);

        $this->assertEquals(parse_url($url, PHP_URL_HOST), $link->getHostname());
    }
}
