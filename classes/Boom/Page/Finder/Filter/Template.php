<?php

namespace Boom\Page\Finder\Filter;

use \Boom\Template\Template as Template;

class Template extends \Boom\Finder\Filter
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

	public function execute(\ORM $query)
	{
		return $query->where('template_id', '=', $this->template->getId());
	}
}