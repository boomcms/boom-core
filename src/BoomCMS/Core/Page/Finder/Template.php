<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Template\Template;
use BoomCMS\Core\Finder\Filter;
use BoomCMS\Core\Model\Page as Model;

use Illuminate\Database\Eloquent\Builder;

class Template extends Filter
{
    /**
     *
     * @var \Boom\Template
     */
    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function execute(Builder $query)
    {
        return $query->where('template_id', '=', $this->template->getId());
    }
}
