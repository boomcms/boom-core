<?php

namespace BoomCMS\Tests\Page\History;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Page\History\Diff;
use BoomCMS\Tests\AbstractTestCase;

class DiffTest extends AbstractTestCase
{
    /**
     *
     * @var Diff
     */
    protected $diff;

    /**
     * @var PageVersion
     */
    protected $new;

    /**
     * @var PageVersion
     */
    protected $old;

    public function setUp()
    {
        $this->diff = new Diff();
        $this->new = new PageVersion();
        $this->old = new PageVersion();
    }

    public function testCompareTemplateChange()
    {
        $this->new->{PageVersion::ATTR_TEMPLATE} = 1;
        $this->old->{PageVersion::ATTR_TEMPLATE} = 2;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\TemplateChange::class, $result);  
    }

    public function testCompareTitleChange()
    {
        $this->new->{PageVersion::ATTR_TITLE} = 'test';
        $this->old->{PageVersion::ATTR_TITLE} = 'test2';

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\TitleChange::class, $result);  
    }

    public function testCompareChunkChange()
    {
        $this->new->{PageVersion::ATTR_CHUNK_TYPE} = 'text';
        $this->new->{PageVersion::ATTR_CHUNK_ID} = 1;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\ChunkChange::class, $result);  
    }
}
