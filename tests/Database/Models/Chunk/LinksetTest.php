<?php

namespace BoomCMS\Tests\Database\Models\Chunk\Linkset;

use BoomCMS\Database\Models\Chunk\Linkset;
use BoomCMS\Tests\AbstractTestCase;

class LinksetTest extends AbstractTestCase
{
    public function testTextIsCleaned()
    {
        $values = [
            ' test '      => 'test',
            '<p>test</p>' => 'test',
        ];

        foreach ($values as $value => $cleaned) {
            $linkset = new Linkset();
            $linkset->{Linkset::ATTR_LINKS} = [
                ['text' => $value],
            ];

            $links = $linkset->{Linkset::ATTR_LINKS};

            $this->assertEquals($cleaned, $links[0]['text']);
        }
    }
}
