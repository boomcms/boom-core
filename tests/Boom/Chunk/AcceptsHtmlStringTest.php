<?php

use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

class AcceptsHtmlStringTest extends TestCase
{
    public function testSetsHtml()
    {
        $chunk = $this->getObjectForTrait(AcceptsHtmlString::class);
        $html = '<test></test>';

        $chunk->setHtml($html);
        $this->assertEquals($html, $chunk->getHtmlTemplate());
    }
}