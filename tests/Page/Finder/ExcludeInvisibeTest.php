<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Core\Page\Finder\ExcludeInvisible as Filter;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class ExcludeInvisibeTest extends AbstractTestCase
{
    public function testHandle()
    {
        $query = m::mock(Builder::class);
        $query
            ->shouldReceive('isVisibleAtTime')
            ->once()
            ->with(time());

        $filter = new Filter();

        $filter->build($query);
    }

    public function testEditorIsDisabledShouldBeAppliedByDefault()
    {
        Editor::shouldReceive('isEnabled')
            ->once()
            ->andReturn(false);

        $filter = new Filter();

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testInvisiblePagesAreHiddenSoEditorStateIsIgnored()
    {
        Editor::shouldReceive('isEnabled')->never();

        $filter = new Filter(true);

        $this->assertTrue($filter->shouldBeApplied());
    }

    public function testShouldNotBeAppliedIfSetToFalseButEditorIsDisabled()
    {
        Editor::shouldReceive('isEnabled')->never();

        $filter = new Filter(false);

        $this->assertFalse($filter->shouldBeApplied());
    }
}
