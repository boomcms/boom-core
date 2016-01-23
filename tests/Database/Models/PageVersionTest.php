<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\PageVersion as Version;
use BoomCMS\Database\Models\Person;
use DateTime;

class PageVersionTest extends AbstractModelTestCase
{
    protected $model = Version::class;

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

    /**
     * @depends testIsPublishedIfEmbargoedTimeIsPast
     */
    public function testMakeDraft()
    {
        $version = new Version([Version::ATTR_EMBARGOED_UNTIL => time() - 10]);
        $version->makeDraft();

        $this->assertTrue($version->isDraft());
        $this->assertFalse($version->isPublished());
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

    public function testSetEditedAt()
    {
        $version = new Version();
        $time = new DateTime('now');

        $this->assertEquals($version, $version->setEditedAt($time));
        $this->assertEquals($time->getTimestamp(), $version->{Version::ATTR_EDITED_AT});
    }

    public function testSetEditedBy()
    {
        $version = new Version();

        $person = new Person();
        $person->{Person::ATTR_ID} = 1;

        $this->assertEquals($version, $version->setEditedBy($person));
        $this->assertEquals($person->getId(), $version->{Version::ATTR_EDITED_BY});
    }

    public function testSetPage()
    {
        $version = new Version();

        $page = new Page();
        $page->{Page::ATTR_ID} = 1;

        $this->assertEquals($version, $version->setPage($page));
        $this->assertEquals($page->getId(), $version->{Version::ATTR_PAGE});
    }

    /**
     * Page titles over 100 characters should not be saved.
     */
    public function testSetTitleIsIgnoredIfTooLong()
    {
        $version = new Version();
        $version->title = str_random(101);

        $this->assertEquals('', $version->title);
    }

    public function testTitleIsSetIfLessThan100Characters()
    {
        $title = str_random(99);

        $version = new Version();
        $version->title = $title;

        $this->assertEquals($title, $version->title);
    }

    /**
     * Page title should be trimmed and have HTML removed.
     */
    public function testTitleIsCleaned()
    {
        $version = new Version();
        $version->title = ' <b>test</b> ';

        $this->assertEquals('test', $version->title);
    }
}
