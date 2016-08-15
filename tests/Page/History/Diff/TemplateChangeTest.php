<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Database\Models\Template;
use BoomCMS\Page\History\Diff\TemplateChange;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

class TemplateChangeTest extends AbstractTestCase
{
   public function testDescriptionKeyExists()
    {
        $class = new TemplateChange(m::mock(PageVersion::class), m::mock(PageVersion::class));

        $this->assertTrue(Lang::has($class->getDescriptionKey()));
    }

    public function testGetDescriptionParams()
    {
        $new = m::mock(PageVersion::class);
        $old = m::mock(PageVersion::class);

        $newTemplate = new Template([Template::ATTR_NAME => 'new template']);
        $oldTemplate = new Template([Template::ATTR_NAME => 'old template']);

        $new
            ->shouldReceive('getTemplate')
            ->once()
            ->andReturn($newTemplate);

        $old
            ->shouldReceive('getTemplate')
            ->once()
            ->andReturn($oldTemplate);

        $change = new TemplateChange($new, $old);
        $params = [
            'new' => $newTemplate->getName(),
            'old' => $oldTemplate->getName(),
        ];

        $this->assertEquals($params, $change->getDescriptionParams());
    }
}
