<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Provider;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class ChunkProviderTest extends AbstractTestCase
{
    public function testAllowedToEditorWhenNoPageIsGiven()
    {
        $chunk = m::mock(Provider::class)->makePartial();

        $this->assertTrue($chunk->allowedToEdit());
    }
}
