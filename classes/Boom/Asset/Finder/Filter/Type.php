<?php

namespace Boom\Asset\Finder\Filter;

class Type extends \Boom\Finder\Filter
{
	protected $_type;

	public function __construct($types = null)
	{
		$this->_type = is_array($types)?: array($types);
	}

	public function execute(\ORM $query)
	{
		foreach ($this->_type as & $type) {
			if ( ! is_int($type) && ! ctype_digit($type)) {
				$type = constant('\Boom\Asset\Type::' . strtoupper($type));
			}
		}

		return $query->where('asset.type', 'in', $this->_type);
	}

	public function shouldBeApplied()
	{
		return ! empty($this->_type);
	}
}