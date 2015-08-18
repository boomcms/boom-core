<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Core\Template\Template as TemplateObject;
use Illuminate\Database\Eloquent\Builder;

class Template extends Filter
{
    /**
     * @var TemplateObject
     */
    protected $template;

    public function __construct(TemplateObject $template)
    {
        $this->template = $template;
    }

    public function build(Builder $query)
    {
        return $query->where('template_id', '=', $this->template->getId());
    }
}
