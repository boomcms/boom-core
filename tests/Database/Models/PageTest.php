<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use BoomCMS\Support\Helpers\URL as URLHelper;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Mockery as m;

class PageTest extends AbstractModelTestCase
{
    protected $model = Page::class;

    public function testAclEnabled()
    {
        $values = [
            null => false,
            1    => true,
            0    => false,
        ];

        foreach ($values as $value => $enabled) {
            $page = new Page([Page::ATTR_ENABLE_ACL => $value]);

            $this->assertEquals($enabled, $page->aclEnabled());
        }
    }

    /**
     * @depends testAclEnabled
     */
    public function testSetAclEnabled()
    {
        $page = new Page();

        $page->setAclEnabled(true);
        $this->assertTrue($page->aclEnabled());

        $page->setAclEnabled(false);
        $this->assertFalse($page->aclEnabled());
    }

    public function testAddAclGroupId()
    {
        $groupId = 1;
        $page = m::mock(Page::class)->makePartial();

        $query = DB::shouldReceive('table')
            ->once()
            ->with('page_acl')
            ->andReturnSelf();

        $query
            ->shouldReceive('insert')
            ->once()
            ->with([
                'page_id'  => $page->getId(),
                'group_id' => $groupId,
            ])
            ->andReturnSelf();

        $page->addAclGroupId($groupId);
    }

    public function testRemoveAclGroupId()
    {
        $groupId = 1;
        $page = m::mock(Page::class)->makePartial();

        $query = DB::shouldReceive('table')
            ->once()
            ->with('page_acl')
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with([
                'page_id'  => $page->getId(),
                'group_id' => $groupId,
            ])
            ->andReturnSelf();

        $query
            ->shouldReceive('delete')
            ->once();

        $page->removeAclGroupId($groupId);
    }

    public function testCanBeDeleted()
    {
        $values = [
            true  => false,
            false => true,
            null  => true,
        ];

        foreach ($values as $disableDelete => $canBeDeleted) {
            $page = new Page([Page::ATTR_DISABLE_DELETE => $disableDelete]);

            $this->assertEquals($canBeDeleted, $page->canBeDeleted());
        }
    }

    public function testGetAclGroupIds()
    {
        $collection = collect([1, 2]);
        $pageId = 1;
        $page = m::mock(Page::class)->makePartial();

        $page
            ->shouldReceive('getId')
            ->once()
            ->andReturn($pageId);

        $query = DB::shouldReceive('table')
            ->once()
            ->with('page_acl')
            ->andReturnSelf();

        $query
            ->shouldReceive('select')
            ->once()
            ->with('group_id')
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with('page_id', $pageId)
            ->andReturnSelf();

        $query
            ->shouldReceive('pluck')
            ->once()
            ->with('group_id')
            ->andReturn($collection);

        $page->getAclGroupIds();
    }

    public function testGetAddPageBehaviour()
    {
        $page = new Page([Page::ATTR_ADD_BEHAVIOUR => Page::ADD_PAGE_CHILD]);
        $this->assertEquals(Page::ADD_PAGE_CHILD, $page->getAddPageBehaviour());
    }

    public function testGetAddPageBehaviourDefaultIsNone()
    {
        $page = new Page();
        $this->assertEquals(Page::ADD_PAGE_NONE, $page->getAddPageBehaviour());
    }

    public function testGetChildAddPageBehaviour()
    {
        $page = new Page([Page::ATTR_CHILD_ADD_BEHAVIOUR => Page::ADD_PAGE_CHILD]);

        $this->assertEquals(Page::ADD_PAGE_CHILD, $page->getChildAddPageBehaviour());
    }

    /**
     * The add page parent is the current page if the add page behaviour is to add a child
     * Or it's to add a sibling but the page doesn't have a parent.
     */
    public function testGetAddPageParentReturnsSelf()
    {
        $page = m::mock(Page::class.'[isRoot]');

        $page
            ->shouldReceive('isRoot')
            ->twice()
            ->andReturn(true);

        foreach ([Page::ADD_PAGE_CHILD, Page::ADD_PAGE_SIBLING, Page::ADD_PAGE_NONE] as $behaviour) {
            $page->{Page::ATTR_ADD_BEHAVIOUR} = $behaviour;

            $this->assertEquals($page, $page->getAddPageParent());
        }
    }

    public function testGetAddPageParentReturnsItsParent()
    {
        $parent = new Page();
        $page = m::mock(Page::class.'[isRoot,getParent]');
        $page->{Page::ATTR_ADD_BEHAVIOUR} = Page::ADD_PAGE_SIBLING;

        $page
            ->shouldReceive('isRoot')
            ->once()
            ->andReturn(false);

        $page
            ->shouldReceive('getParent')
            ->once()
            ->andReturn($parent);

        $this->assertEquals($parent, $page->getAddPageParent());
    }

    /**
     * getAddPageParent() should return null when the behaviour is to prompt and it doesn't have a parent
     * since the parent is then determined by the user response.
     */
    public function testGetAddPageParentInheritsSettingFromParent()
    {
        $parent = new Page();
        $page = m::mock(Page::class.'[isRoot,getParent]');

        $values = [
            Page::ADD_PAGE_CHILD   => $page,
            Page::ADD_PAGE_SIBLING => $parent,
            Page::ADD_PAGE_NONE    => $page,
        ];

        $page->{Page::ATTR_ADD_BEHAVIOUR} = Page::ADD_PAGE_NONE;
        $page
            ->shouldReceive('isRoot')
            ->andReturn(false);

        $page
            ->shouldReceive('getParent')
            ->andReturn($parent);

        foreach ($values as $v => $addParent) {
            $parent->{Page::ATTR_CHILD_ADD_BEHAVIOUR} = $v;

            $this->assertEquals($addParent, $page->getAddPageParent());
        }
    }

    public function testGetChildAddPageBehaviourDefaultIsNone()
    {
        $page = new Page();
        $this->assertEquals(Page::ADD_PAGE_NONE, $page->getChildAddPageBehaviour());
    }

    public function testGetChildOrderingPolicy()
    {
        $values = [
            Page::ORDER_TITLE                           => ['title', 'desc'], // Default is descending
            Page::ORDER_TITLE | Page::ORDER_ASC         => ['title', 'asc'],
            Page::ORDER_TITLE | Page::ORDER_DESC        => ['title', 'desc'],
            Page::ORDER_VISIBLE_FROM | Page::ORDER_ASC  => ['visible_from', 'asc'],
            Page::ORDER_VISIBLE_FROM | Page::ORDER_DESC => ['visible_from', 'desc'],
            Page::ORDER_SEQUENCE | Page::ORDER_ASC      => ['sequence', 'asc'],
            Page::ORDER_SEQUENCE | Page::ORDER_DESC     => ['sequence', 'desc'],
            0                                           => ['sequence', 'desc'],
        ];

        foreach ($values as $order => $expected) {
            $page = new Page([Page::ATTR_CHILD_ORDERING_POLICY => $order]);

            $this->assertEquals($expected, $page->getChildOrderingPolicy());
        }
    }

    public function testGetDefaultChildTemplateIdReturnsInt()
    {
        $page = new Page();
        $page->{Page::ATTR_CHILD_TEMPLATE} = '1';

        $this->assertEquals(1, $page->getDefaultChildTemplateId());
        $this->assertInternalType('integer', $page->getDefaultChildTemplateId());
    }

    /**
     * Give a page with no parent and no default child template ID.
     *
     * getDefaultChildTemplateId should return the page's template ID
     */
    public function testGetDefaultChildTemplateIdAtRootPage()
    {
        $page = m::mock(Page::class)->makePartial();
        $page->shouldReceive('getParent')->once()->andReturnNull();
        $page->shouldReceive('getTemplateId')->once()->andReturn(1);

        $this->assertEquals(1, $page->getDefaultChildTemplateId());
    }

    public function testGetDefaultGrandchildTemplateIdReturnsTemplateId()
    {
        $values = [0, null, ''];
        $templateId = 2;

        $page = m::mock(Page::class)->makePartial();
        $page->shouldReceive('getTemplateId')->times(3)->andReturn($templateId);

        foreach ($values as $grandchildTemplateId) {
            $page->{Page::ATTR_GRANDCHILD_TEMPLATE} = $grandchildTemplateId;

            $this->assertEquals($templateId, $page->getDefaultGrandchildTemplateId());
        }
    }

    public function testGetDefaultGrandchildTemplateIdReturnsDefinedValue()
    {
        $values = [1, 2, 3];

        $page = m::mock(Page::class)->makePartial();
        $page->shouldReceive('getTemplateId')->never();

        foreach ($values as $grandchildTemplateId) {
            $page->{Page::ATTR_GRANDCHILD_TEMPLATE} = $grandchildTemplateId;

            $this->assertEquals($grandchildTemplateId, $page->getDefaultGrandchildTemplateId());
        }
    }

    public function testGetGrandchildTemplateIdReturnsInt()
    {
        $page = new Page();
        $page->{Page::ATTR_GRANDCHILD_TEMPLATE} = '1';

        $this->assertEquals(1, $page->getGrandchildTemplateId());
        $this->assertInternalType('integer', $page->getGrandchildTemplateId());
    }

    public function testGetFeatureImageId()
    {
        $page = new Page([Page::ATTR_FEATURE_IMAGE => 1]);
        $this->assertEquals(1, $page->getFeatureImageId());

        $page = new Page();
        $this->assertNull($page->getFeatureImageId());
    }

    public function testGetFeatureImage()
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('first')->once();

        $page = m::mock(Page::class.'[belongsTo]');
        $page
            ->shouldReceive('belongsTo')
            ->once()
            ->with(Asset::class, 'feature_image_id')
            ->andReturn($builder);

        $page->getFeatureImage();
    }

    public function testGetDescriptionReturnsDescriptionIfSet()
    {
        $page = new Page([Page::ATTR_DESCRIPTION => 'test']);
        $this->assertEquals('test', $page->getDescription());
    }

    public function testGetDescriptionReturnsStringIfEmpty()
    {
        $page = new Page();
        $this->assertEquals('', $page->getDescription());
    }

    public function testGetDescriptionRemovesHtml()
    {
        $page = new Page([Page::ATTR_DESCRIPTION => '<p>description</p>']);
        $this->assertEquals('description', $page->getDescription());
    }

    public function testGetParentWithNoParentId()
    {
        $page = new Page([Page::ATTR_PARENT => null]);

        $this->assertNull($page->getParent());
    }

    public function testGetSite()
    {
        $site = new Site();
        $page = m::mock(Page::class.'[belongsTo,first]');

        $page
            ->shouldReceive('belongsTo')
            ->once()
            ->with(Site::class, 'site_id')
            ->andReturnSelf();

        $page
            ->shouldReceive('first')
            ->once()
            ->andReturn($site);

        $this->assertEquals($site, $page->getSite());
    }

    public function testHasFeatureImage()
    {
        $page = new Page([Page::ATTR_FEATURE_IMAGE => 1]);
        $this->assertTrue($page->hasFeatureImage());

        $page = new Page();
        $this->assertFalse($page->hasFeatureImage());
    }

    public function testIsDeleted()
    {
        $values = [
            0      => false,
            null   => false,
            time() => true,
        ];

        foreach ($values as $deletedAt => $isDeleted) {
            $page = new Page(['deleted_at' => $deletedAt]);

            $this->assertEquals($isDeleted, $page->isDeleted());
        }
    }

    public function testIsVisibleAtAnyTime()
    {
        $yes = [1, true];
        $no = [0, false, null];

        foreach ($yes as $y) {
            $page = new Page([Page::ATTR_VISIBLE => $y]);
            $this->assertTrue($page->isVisibleAtAnyTime(), $y);
        }

        foreach ($no as $n) {
            $page = new Page([Page::ATTR_VISIBLE => $n]);
            $this->assertFalse($page->isVisibleAtAnyTime(), $n);
        }
    }

    public function testScopeIsVisibleAtTime()
    {
        $time = time();
        $query = m::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_VISIBLE, '=', true)
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_VISIBLE_FROM, '<=', time())
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->andReturnUsing(function ($callback) use ($query) {
                return $callback($query);
            });

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Page::ATTR_VISIBLE_TO, '>=', $time)
            ->andReturnSelf();

        $query
            ->shouldReceive('orWhere')
            ->once()
            ->with(Page::ATTR_VISIBLE_TO, '=', 0)
            ->andReturnSelf();

        $page = new Page();
        $page->scopeIsVisibleAtTime($query, $time);
    }

    public function testSetAddPageBehaviour()
    {
        $page = new Page();

        $this->assertEquals($page, $page->setAddPageBehaviour(Page::ADD_PAGE_CHILD));
        $this->assertEquals(Page::ADD_PAGE_CHILD, $page->getAddPageBehaviour());
    }

    public function testSetChildAddPageBehaviour()
    {
        $page = new Page();

        $this->assertEquals($page, $page->setChildAddPageBehaviour(Page::ADD_PAGE_CHILD));
        $this->assertEquals(Page::ADD_PAGE_CHILD, $page->getChildAddPageBehaviour());
    }

    public function testSetAndGetChildOrderingPolicy()
    {
        $values = [
            ['title', 'asc'],
            ['title', 'desc'],
            ['visible_from', 'asc'],
            ['visible_from', 'desc'],
            ['sequence', 'asc'],
            ['sequence', 'desc'],
        ];

        foreach ($values as $v) {
            list($column, $direction) = $v;

            $page = new Page();
            $page->setChildOrderingPolicy($column, $direction);
            list($newCol, $newDirection) = $page->getChildOrderingPolicy();

            $this->assertEquals($column, $newCol);
            $this->assertEquals($direction, $newDirection);
        }
    }

    public function testSetCurrentVersion()
    {
        $page = new Page();
        $version = new PageVersion();

        $this->assertEquals($page, $page->setCurrentVersion($version));
        $this->assertEquals($version, $page->getCurrentVersion());
    }

    /**
     * A page internal name can contain lowercase letters, underscores, hyphens, or numbers.
     *
     * All other characters should be removed.
     */
    public function testSetInternalNameRemovesInvalidCharacters()
    {
        $page = new Page();

        $values = [
            '404'      => '404',
            ' test '   => 'test',
            'test'     => 'test',
            '£$%^&*()' => '',
            'TEST'     => 'test',
        ];

        foreach ($values as $in => $out) {
            $page->setInternalName($in);

            $this->assertEquals($out, $page->getInternalName());
        }
    }

    public function testSetParentPageCantBeChildOfItself()
    {
        $page = new Page([Page::ATTR_PARENT => 2]);
        $page->{Page::ATTR_ID} = 1;
        $page->setParent($page);

        $this->assertEquals(2, $page->getParentId());
    }

    public function testSetPrimaryUriIsSantized()
    {
        $url = '% not /// a good URL.';
        $page = new Page();
        $page->{Page::ATTR_PRIMARY_URI} = $url;

        $this->assertEquals(URLHelper::sanitise($url), $page->getAttribute(Page::ATTR_PRIMARY_URI));
    }

    public function testSetVisibleAtAnyTime()
    {
        $values = [
            1     => true,
            true  => true,
            0     => false,
            false => false,
        ];

        foreach ($values as $value => $expected) {
            $page = new Page();
            $page->setVisibleAtAnyTime($value);

            $this->assertEquals($expected, $page->isVisibleAtAnyTime());
        }
    }

    public function testSetVisibleFrom()
    {
        $page = new Page();
        $time = new DateTime('now');

        $page->setVisibleFrom($time);
        $this->assertEquals($time->getTimestamp(), $page->{Page::ATTR_VISIBLE_FROM});

        $page->setVisibleFrom(null);
        $this->assertNull($page->{Page::ATTR_VISIBLE_FROM});
    }

    public function testHasChildren()
    {
        $hasMany = m::mock(HasMany::class);
        $hasMany
            ->shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $hasMany
            ->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        $page = m::mock(Page::class)->makePartial();
        $page
            ->shouldReceive('children')
            ->times(2)
            ->andReturn($hasMany);

        $this->assertFalse($page->hasChildren());
        $this->assertTrue($page->hasChildren());
    }

    public function testIsParentOf()
    {
        $parent = $this->validPage();

        $child = new Page([Page::ATTR_PARENT => $parent->getId()]);
        $notAChild = new Page([Page::ATTR_PARENT => 2]);

        $this->assertTrue($parent->isParentOf($child), 'Child');
        $this->assertFalse($parent->isParentOf($notAChild), 'Not child');
    }

    public function testSetSequence()
    {
        $page = new Page();

        $this->assertEquals($page, $page->setSequence(2));
        $this->assertEquals(2, $page->getManualOrderPosition());
    }

    public function testUrlReturnsNullIfNoPrimaryUri()
    {
        $page = new Page();

        $this->assertNull($page->url());
    }

    public function testUrlReturnsUrlObject()
    {
        $page = new Page([Page::ATTR_PRIMARY_URI => 'test']);
        $url = $page->url();

        $this->assertInstanceOf(URL::class, $url);
        $this->assertEquals('test', $url->getLocation());
    }
}
