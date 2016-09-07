<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Page\Finder\Acl;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class AclFilterTest extends AbstractTestCase
{
    /**
     * @var Person
     */
    protected $person;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var Acl
     */
    protected $filter;

    public function setUp()
    {
        parent::setUp();

        $this->person = m::mock(Person::class);
        $this->site = m::mock(Site::class);
        $this->query = m::mock(Builder::class);

        $this->filter = new Acl($this->site, $this->person);
    }

    public function testShouldNotBeAppliedIfCurrentUserCanManagePages()
    {
        $this->person
            ->shouldReceive('can')
            ->once()
            ->with('managePages', $this->site)
            ->andReturn(true);

        $this->assertFalse($this->filter->shouldBeApplied());
    }

    public function testPagesWithAclEnabledIgnoredIfCurrentUserIsGuest()
    {
        $filter = new Acl($this->site);

        $this->query
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_ENABLE_ACL, false)
            ->andReturnSelf();

        $this->assertEquals($this->query, $filter->build($this->query));
    }

    public function testLoggedInSoFilterPagesByAcl()
    {
        $personId = 1;

        $this->person
            ->shouldReceive('can')
            ->once()
            ->with('managePages', $this->site)
            ->andReturn(false);

        $this->person
            ->shouldReceive('getId')
            ->twice()
            ->andReturn($personId);

        $this->query
            ->shouldReceive('leftJoin')
            ->once()
            ->with('group_person', 'page_acl.group_id', '=', 'group_person.group_id')
            ->andReturnSelf();

        $this->query
            ->shouldReceive('leftJoin')
            ->once()
            ->with('page_acl', 'pages.id', '=', 'page_acl.page_id')
            ->andReturnSelf();

        $this->query
            ->shouldReceive('where')
            ->once()
            ->with(m::on(function ($closure) use ($personId) {
                $this->query
                    ->shouldReceive('where')
                    ->once()
                    ->with(Page::ATTR_CREATED_BY, $personId)
                    ->andReturnSelf();

                $this->query
                    ->shouldReceive('orWhereNull')
                    ->once()
                    ->with('page_acl.group_id')
                    ->andReturnSelf();

                $this->query
                    ->shouldReceive('orWhere')
                    ->once()
                    ->with('group_person.person_id', $personId)
                    ->andReturnSelf();

                $closure($this->query);

                return true;
            }))
            ->andReturnSelf();

        $this->assertTrue($this->filter->shouldBeApplied());
        $this->assertEquals($this->query, $this->filter->build($this->query));
    }
}
