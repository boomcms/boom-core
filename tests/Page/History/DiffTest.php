<?php

namespace BoomCMS\Tests\Page\History;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Page\History\Diff;
use BoomCMS\Tests\AbstractTestCase;
use DateTime;

class DiffTest extends AbstractTestCase
{
    /**
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

    /**
     * Approval was requested if the new version is pending approval
     * but the old version is not
     * 
     */
    public function testApprovalRequested()
    {
        $this->new->{PageVersion::ATTR_PENDING_APPROVAL} = true;
        $this->old->{PageVersion::ATTR_PENDING_APPROVAL} = false;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\ApprovalRequest::class, $result);
    }

    /**
     * An embargo time was set if the old version wasn't embargoed but the new one is
     * 
     */
    public function testEmbargoSet()
    {
        $time = new DateTime('@'.time());

        $this->new->{PageVersion::ATTR_EMBARGOED_UNTIL} = $time->getTimestamp() + 1000;
        $this->new->{PageVersion::ATTR_EDITED_AT} = $time->getTimestamp();

        $this->old->{PageVersion::ATTR_EMBARGOED_UNTIL} = null;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\Embargoed::class, $result);
    }

    /**
     * The embargo time was changed if:
     * 
     *   * Both versions are embargoed
     *   * The embargo times aren't the same
     * 
     */
    public function testEmbargoChanged()
    {
        $time = new DateTime('@'.time());

        $this->new->{PageVersion::ATTR_EMBARGOED_UNTIL} = $time->getTimestamp() + 1000;
        $this->new->{PageVersion::ATTR_EDITED_AT} = $time->getTimestamp();

        $this->old->{PageVersion::ATTR_EMBARGOED_UNTIL} = $time->getTimestamp() - 500;
        $this->old->{PageVersion::ATTR_EDITED_AT} = $time->getTimestamp() - 1000;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\EmbargoChanged::class, $result);
    }

    /**
     * Changes were published if the new version is published but the previous one ins't
     * 
     */
    public function testPublishedAfterDraft()
    {
        $this->new->{PageVersion::ATTR_EMBARGOED_UNTIL} = time();
        $this->new->{PageVersion::ATTR_EDITED_AT} = time();

        $this->old->{PageVersion::ATTR_EMBARGOED_UNTIL} = null;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\Published::class, $result);
    }

    /**
     * Changes were published if the new version is published but the previous one ins't
     * 
     */
    public function testPublishedAfterApprovalRequest()
    {
        $this->new->{PageVersion::ATTR_EMBARGOED_UNTIL} = time();
         $this->new->{PageVersion::ATTR_EDITED_AT} = time();

        $this->old->{PageVersion::ATTR_PENDING_APPROVAL} = true;
        $this->old->{PageVersion::ATTR_EMBARGOED_UNTIL} = null;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\Published::class, $result);
    }

    /**
     * Changes were published if the new version is published but the previous one ins't
     * 
     */
    public function testPublishedAfterEmbargoed()
    {
        $this->new->{PageVersion::ATTR_EMBARGOED_UNTIL} = time();
         $this->new->{PageVersion::ATTR_EDITED_AT} = time();

        $this->old->{PageVersion::ATTR_EMBARGOED_UNTIL} = time() - 500;
        $this->old->{PageVersion::ATTR_EDITED_AT} = time() - 1000;

        $result = $this->diff->compare($this->new, $this->old);

        $this->assertInstanceOf(Diff\Published::class, $result);
    }
}
