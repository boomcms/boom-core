<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Page\History\Diff\TitleChange;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

class TitleChangeTest extends AbstractTestCase
{
    public function testDescriptionKeyExists()
    {
        $class = new TitleChange(m::mock(PageVersion::class), m::mock(PageVersion::class));

        $this->assertTrue(Lang::has($class->getDescriptionKey()));
    }

    public function testGetDescriptionParams()
    {
        $new = new PageVersion([PageVersion::ATTR_TITLE => 'new']);
        $old = new PageVersion([PageVersion::ATTR_TITLE => 'old']);

        $change = new TitleChange($new, $old);

        $params = [
            'new' => $new->getTitle(),
            'old' => $old->getTitle(),
        ];

        $this->assertEquals($params, $change->getDescriptionParams());
    }
}
