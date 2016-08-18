<?php

namespace BoomCMS\Tests\Page\Finder;

use BoomCMS\Database\Models\Template;
use BoomCMS\Page\Finder\Template as Filter;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class TemplateTest extends AbstractTestCase
{
    public function testAcceptsNullButWontBeApplied()
    {
        $filter = new Filter();

        $this->assertFalse($filter->shouldBeApplied());
    }

    public function testAcceptsTemplateId()
    {
        $templateId = 1;
        $template = new Template();
        $template->{Template::ATTR_ID} = $templateId;

        TemplateFacade::shouldReceive($templateId)->once()->andReturn($template);

        $query = m::mock(Builder::class);
        $query->shouldReceive('where')
            ->once()
            ->with('template_id', '=', $templateId);

        $filter = new Filter($template);

        $this->assertTrue($filter->shouldBeApplied());

        $filter->build($query);
    }

    public function testIsAppliedWithTemplate()
    {
        $template = new Template();
        $template->{Template::ATTR_ID} = 1;

        $query = m::mock(Builder::class);
        $query->shouldReceive('where')
            ->once()
            ->with('template_id', '=', $template->getId());

        $filter = new Filter($template);

        $this->assertTrue($filter->shouldBeApplied());

        $filter->build($query);
    }
}
