<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class Template extends Finder\Filter
{
	/**
	 *
	 * @var \Model_Template
	 */
	protected $_template;

	public function __construct(\Model_Template $template)
	{
		$this->_template = $template;
	}

	public function execute(\ORM $query)
	{
		return $query->where('template_id', '=', $this->_template->id);
	}
}