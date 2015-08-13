<?php

use BoomCMS\Core\Chunk\Html as Chunk;
use BoomCMS\Core\Page\Page;
use BoomCMS\Database\Models\Chunk\Html as Model;

class Chunk_HtmlTest extends TestCase
{
    public function testHasContentIfHtmlAttributeHasContent()
    {
        $page = new Page([]);
        $chunk = new Chunk($page, [], 'test');
        $this->assertFalse($chunk->hasContent());

        $chunk = new Chunk($page, ['html' => '     '], 'test');
        $this->assertFalse($chunk->hasContent());

        $chunk = new Chunk($page, ['html' => 'asfsf'], 'test');
        $this->assertTrue($chunk->hasContent());
    }

    public function testGetContentReturnsHtml()
    {
        $html = 'asfsf';
        $chunk = new Chunk(new Page([]), ['html' => $html], 'test');
        $this->assertEquals($html, $chunk->getContent());
    }

    public function testHtmlIsTrimmedBeforeSave()
    {
        $model = new Model();
        $model->html = '  test  ';

        $this->assertEquals('test', $model->html);
    }
}
