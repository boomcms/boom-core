<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Page\History\Diff\ChunkChange;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

class ChunkChangeTest extends AbstractTestCase
{
   public function testDescriptionKeyExists()
    {
        $class = new ChunkChange(m::mock(PageVersion::class), m::mock(PageVersion::class));

        $this->assertTrue(Lang::has($class->getDescriptionKey()));
    }
}
