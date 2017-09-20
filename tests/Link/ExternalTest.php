<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Link\External as Link;
use BoomCMS\Tests\AbstractTestCase;

class ExternalTest extends AbstractTestCase
{
    public function testGetAssetIdReturnsZeroForEmptyString()
    {
        $link = new Link('http://www.boomcms.net', ['asset_id' => '']);

        $this->assertEquals(0, $link->getAssetId());
    }

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
        $attrs = [
            ['title' => ''],
            ['title' => null],
            [],
        ];

        foreach ($attrs as $a) {
            $links = [
                'www.google.com/test',
                'www.google.com/test?test=test#test',
            ];

            foreach ($links as $l) {
                $link = new Link($l, $a);

                $this->assertEquals($l, $link->getTitle());
            }
        }
    }

    /**
     * @depends testGetTitleReturnsTheUrl
     */
    public function testGetTitleTitleReturnsTheUrlWithoutTheProtocol()
    {
        $protocols = ['http://', 'https://', 'mailto:', 'tel:'];

        foreach ($protocols as $protocol) {
            $url = 'test'; // Doesn't really matter whether the URL is valid or not

            $link = new Link($protocol.$url);

            $this->assertEquals($url, $link->getTitle());
        }
    }

    public function testGetTitleReturnsTitleAttribute()
    {
        $links = [
            'http://www.google.com/test',
            'www.google.com/test',
            'www.google.com/test?test=test#test',
        ];

        foreach ($links as $l) {
            $title = 'link title';
            $link = new Link($l, ['title' => $title]);

            $this->assertEquals($title, $link->getTitle());
        }
    }

    public function testGetHostnameReturnsTheHostname()
    {
        $url = 'http://www.boomcms.net/hello';
        $link = new Link($url);

        $this->assertEquals(parse_url($url, PHP_URL_HOST), $link->getHostname());
    }

    /**
     * External links currently can't have a featured asset.
     */
    public function testGetFeatureImageIdReturnsZero()
    {
        $link = new Link('');

        $this->assertEquals(0, $link->getFeatureImageId());
    }

    /**
     * External links currently can't have a featured asset.
     */
    public function testGetTextReturnsTextAttribute()
    {
        $text = 'test';
        $link = new Link('', ['text' => $text]);

        $this->assertEquals($text, $link->getText());
    }

    public function testIsInternalReturnsFalse()
    {
        $link = new Link('');

        $this->assertFalse($link->isInternal());
    }

    public function testIsExternallReturnsTrue()
    {
        $link = new Link('');

        $this->assertTrue($link->isExternal());
    }

    /**
     * External links are always visible.
     */
    public function testIsVisibleReturnsTrue()
    {
        $link = new Link('');

        $this->assertTrue($link->isVisible());
    }

    public function testIsValidReturnsFalseIfLinkIsEmpty()
    {
        $links = [
            '',
            null,
            '#',
        ];

        foreach ($links as $l) {
            $link = new Link($l);

            $this->assertFalse($link->isValid());
        }
    }

    public function testIsValidReturnsTrueLinkString()
    {
        $links = [
            'http://www.boomcms.net',
            'any other string - no validation is performed',
        ];

        foreach ($links as $l) {
            $link = new Link($l);

            $this->assertTrue($link->isValid());
        }
    }
}
