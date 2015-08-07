<?php

use BoomCMS\Core\Page\Version;

class Page_VersionTest extends TestCase
{
    public function testIsPublishedIfEmbargoedTimeIsPast()
    {
        $published = new Version(['embargoed_until' => time() - 10]);
        $this->assertTrue($published->isPublished());
        $this->assertEquals('published', $published->status());
    }

    public function testIsPublishedIfEmbargoedTimeIsCurrent()
    {
        $published = new Version(['embargoed_until' => time()]);
        $this->assertTrue($published->isPublished());
        $this->assertEquals('published', $published->status());
    }

    public function testNotPublishedIfEmbargoIsInFuture()
    {
        $version = new Version(['embargoed_until' => time() + 10]);
        $this->assertFalse($version->isPublished());
        $this->assertNotEquals('published', $version->status());
    }

    public function testNotPublishedIfEmbargoIsNull()
    {
        $version = new Version(['embargoed_until' => null]);
        $this->assertFalse($version->isPublished());
        $this->assertNotEquals('published', $version->status());
    }

    public function testIsDraftIfNoEmbargoTime()
    {
        $version = new Version(['embargoed_until' => null]);
        $this->assertTrue($version->isDraft());
        $this->assertEquals('draft', $version->status());
    }

    public function testNotDraftWithEmbargoTime()
    {
        $version = new Version(['embargoed_until' => time()]);
        $this->assertFalse($version->isDraft());
        $this->assertNotEquals('draft', $version->status());
    }

    public function testIsEmbargoedIfEmbargoInFuture()
    {
        $version = new Version(['embargoed_until' => time() + 10]);
        $this->assertTrue($version->isEmbargoed());
        $this->assertEquals('embargoed', $version->status());
    }

    public function testNotEmbargoedIfEmbargoInPast()
    {
        $version = new Version(['embargoed_until' => time() - 10]);
        $this->assertFalse($version->isEmbargoed());
        $this->assertNotEquals('embargoed', $version->status());
    }

    public function testNotEmbargoedIfNoEmbargo()
    {
        $version = new Version(['embargoed_until' => null]);
        $this->assertFalse($version->isEmbargoed());
        $this->assertNotEquals('embargoed', $version->status());
    }

    public function testPendingApproval()
    {
        $version = new Version(['pending_approval' => true]);
        $this->assertTrue($version->isPendingApproval());
    }
}
