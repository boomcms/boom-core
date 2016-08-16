<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Database\Models\Template;
use BoomCMS\Page\History\Diff\TemplateChange;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class TemplateChangeTest extends AbstractTestCase
{
    protected $className = TemplateChange::class;

    protected $hasNewDescription = true;
    protected $hasOldDescription = true;

    public function testGetOldAndNewDescriptionParams()
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
        $newAttrs = ['template' => $newTemplate->getName()];
        $oldAttrs = ['template' => $oldTemplate->getName()];

        $this->assertEquals($newAttrs, $change->getNewDescriptionParams());
        $this->assertEquals($oldAttrs, $change->getOldDescriptionParams());
    }
}
