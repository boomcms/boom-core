<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Foundation\Chunk\AcceptsHtmlString;
use BoomCMS\Tests\AbstractTestCase;

class AcceptsHtmlStringTest extends AbstractTestCase
{
    public function testSetsHtml()
    {
        $chunk = $this->getObjectForTrait(AcceptsHtmlString::class);
        $html = '<test></test>';

        $chunk->setHtml($html);
        $this->assertEquals($html, $chunk->getHtmlTemplate());
    }
}
