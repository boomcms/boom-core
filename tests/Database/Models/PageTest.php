<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Chunk\Text;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\URL;
use BoomCMS\Support\Facades\Chunk;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class PageTest extends AbstractModelTestCase
{
    protected $model = Page::class;

    public function testGetAddPageBehaviour()
    {
        $page = new Page([Page::ATTR_ADD_BEHAVIOUR => Page::ADD_PAGE_CHILD]);
        $this->assertEquals(Page::ADD_PAGE_CHILD, $page->getAddPageBehaviour());
    }

    public function testGetAddPageBehaviourDefaultIsPrompt()
    {
        $page = new Page();
        $this->assertEquals(Page::ADD_PAGE_PROMPT, $page->getAddPageBehaviour());
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

        foreach ([Page::ADD_PAGE_CHILD, Page::ADD_PAGE_SIBLING, Page::ADD_PAGE_PROMPT] as $behaviour) {
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
            Page::ADD_PAGE_PROMPT  => $page,
        ];

        $page->{Page::ATTR_ADD_BEHAVIOUR} = Page::ADD_PAGE_PROMPT;
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

    public function testGetChildAddPageBehaviourDefaultIsPrompt()
    {
        $page = new Page();
        $this->assertEquals(Page::ADD_PAGE_PROMPT, $page->getChildAddPageBehaviour());
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

    public function testHasFeatureImage()
    {
        $page = new Page([Page::ATTR_FEATURE_IMAGE => 1]);
        $this->assertTrue($page->hasFeatureImage());

        $page = new Page();
        $this->assertFalse($page->hasFeatureImage());
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

    public function testGetDescriptionUsesPageStandfirstAsFallback()
    {
        $page = new Page();

        Chunk::shouldReceive('get')
            ->once()
            ->with('text', 'standfirst', $page)
            ->andReturn(new Text($page, ['text' => 'test standfirst', 'site_text' => 'test standfirst'], 'standfirst', false));

        $this->assertEquals('test standfirst', $page->getDescription());
    }

    public function testGetDescriptionRemovesHtml()
    {
        $page = new Page([Page::ATTR_DESCRIPTION => '<p>description</p>']);
        $this->assertEquals('description', $page->getDescription());
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

    public function testShouldPromptOnAddPageAndChildShouldPromptOnAddPage()
    {
        $values = [
            Page::ADD_PAGE_PROMPT  => true,
            Page::ADD_PAGE_CHILD   => false,
            Page::ADD_PAGE_SIBLING => false,
            // GetAddPageBehaviour should never return anything else
            // But just incase it does anything else should trigger the add page prompt
            null         => true,
            0            => true,
            'asflsdkjfl' => true,
        ];

        foreach ($values as $behaviour => $shouldPrompt) {
            $page = new Page([
                Page::ATTR_ADD_BEHAVIOUR       => $behaviour,
                Page::ATTR_CHILD_ADD_BEHAVIOUR => $behaviour,
            ]);

            $this->assertEquals($shouldPrompt, $page->shouldPromptOnAddPage());
            $this->assertEquals($shouldPrompt, $page->childShouldPromptOnAddPage());
        }
    }

    /**
     * If the page is set to prompt on add page but it has a parent page
     * The page should return the value of shouldPromptOnAddPage of the parent.
     */
    public function testShouldPromptOnAddPageInheritsFromParent()
    {
        $parent = new Page();
        $page = m::mock(Page::class.'[isRoot,getParent]');
        $page->{Page::ATTR_ADD_BEHAVIOUR} = Page::ADD_PAGE_PROMPT;

        $page
            ->shouldReceive('isRoot')
            ->times(3)
            ->andReturn(false);

        $page
            ->shouldReceive('getParent')
            ->times(3)
            ->andReturn($parent);

        $values = [
            Page::ADD_PAGE_PROMPT,
            Page::ADD_PAGE_CHILD,
            Page::ADD_PAGE_SIBLING,
        ];

        foreach ($values as $v) {
            $parent->{Page::ATTR_CHILD_ADD_BEHAVIOUR} = $v;

            $this->assertEquals($parent->childShouldPromptOnAddPage(), $page->shouldPromptOnAddPage());
        }
    }

    public function testHasChildrenReturnsFalseIfChildCountIs0()
    {
        $page = m::mock(Page::class)->makePartial();
        $page
            ->shouldReceive('countChildren')
            ->once()
            ->andReturn(0);

        $this->assertFalse($page->hasChildren());
    }

    public function testHasChildrenReturnsTrueIfChildCountGreaterThan0()
    {
        $page = m::mock(Page::class)->makePartial();
        $page
            ->shouldReceive('countChildren')
            ->once()
            ->andReturn(1);

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
