<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Page\History\Diff\TitleChange;

class TitleChangeTest extends AbstractChangeTestCase
{
    protected $className = TitleChange::class;

    protected $hasNewDescription = true;
    protected $hasOldDescription = true;

    public function testGetDescriptionParams()
    {
        $new = new PageVersion([PageVersion::ATTR_TITLE => 'new']);
        $old = new PageVersion([PageVersion::ATTR_TITLE => 'old']);

        $change = new TitleChange($new, $old);

        $newAttrs = ['title' => $new->getTitle()];
        $oldAttrs = ['title' => $old->getTitle()];

        $this->assertEquals($newAttrs, $change->getNewDescriptionParams());
        $this->assertEquals($oldAttrs, $change->getOldDescriptionParams());
    }
}
