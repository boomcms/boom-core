<?php

namespace BoomCMS\Tests\Models\Chunk;

use BoomCMS\Database\Models\Chunk\Library;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class LibraryTest extends AbstractTestCase
{
    public function testParamsAttributeIsReturnedJsonDecoded()
    {
        $params = [];
        $library = m::mock(Library::class)->makePartial();

        $this->assertEquals($params, $library->getParamsAttribute(json_encode($params)));
    }

    public function testParamsAttributeIsJsonEncoded()
    {
        $params = [];
        $library = m::mock(Library::class)->makePartial();

        $library->params = $params;

        $this->assertEquals(json_encode($params), $library['attributes']['params']);
    }
}
