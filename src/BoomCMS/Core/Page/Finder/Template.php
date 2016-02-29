<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Template extends Filter
{
    /**
     * @var TemplateInterface
     */
    protected $template;

    public function __construct(TemplateInterface $template = null)
    {
        $this->template = $template;
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
