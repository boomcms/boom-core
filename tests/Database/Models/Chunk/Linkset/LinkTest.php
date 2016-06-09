<?php

namespace BoomCMS\Tests\Database\Models\Chunk\Linkset;

use BoomCMS\Database\Models\Chunk\Linkset\Link;
use BoomCMS\Tests\AbstractTestCase;

class LinkTest extends AbstractTestCase
{
    public function testTextIsCleaned()
    {
        $values = [
            ' test '      => 'test',
            '<p>test</p>' => 'test',
        ];

        foreach ($values as $value => $cleaned) {
            $link = new Link();
            $link->{Link::ATTR_TEXT} = $value;

            $this->assertEquals($cleaned, $link->{Link::ATTR_TEXT});
        }
    }
}
