<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use Illuminate\Database\Eloquent\Builder;

class Template extends Filter
{
    /**
     * @var TemplateInterface
     */
    protected $template;

    public function __construct($template = null)
    {
        $this->template = is_numeric($template) ? TemplateFacade::find($template) : $template;
    }

    public function build(Builder $query)
    {
        return $query->where('template_id', '=', $this->template->getId());
    }

    public function shouldBeApplied()
    {
        return $this->template instanceof TemplateInterface;
    }
}
