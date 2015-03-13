<?php

namespace Boom\Page\Finder;

class Template extends \Boom\Finder\Filter
{
    /**
	 *
	 * @var \Boom\Template
	 */
    protected $template;

    public function __construct(\Boom\Template\Template $template)
    {
        $this->template = $template;
    }

    public function execute(\ORM $query)
    {
        return $query->where('template_id', '=', $this->template->getId());
    }
}
