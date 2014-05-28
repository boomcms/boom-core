<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class Template extends Finder\Filter
{
	/**
	 *
	 * @var \Boom\Template
	 */
	protected $template;

	public function __construct(\Boom\Template $template)
	{
		$this->template = $template;
	}

	public function execute(\ORM $query)
	{
		return $query->where('template_id', '=', $this->template->getId());
	}
}