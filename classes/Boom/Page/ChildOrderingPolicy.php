<?php

namespace Boom\Page;

class ChildOrderingPolicy
{
	const MANUAL = 1;
	const ALPHABETIC = 2;
	const DATE = 4;

	const ASC = 8;
	const DESC = 16;

	protected $_column;
	protected $_direction;
	protected $_int;

	public function __construct()
	{
		$numArgs = func_num_args();

		if ($numArgs === 1) {
			$this->_int = func_get_arg(0);

			$this->_setFromInt($this->_int);
		} else if ($numArgs == 2) {
			$this->_column = func_get_arg(0);
			$this->_direction = func_get_arg(1);

			$this->__setFromColumnAndDirection($this->_column, $direction);
		}
	}

	public function asInt()
	{
		return (int) $this->_int;
	}

	public function columnToInt($column)
	{
		switch ($column)
		{
			case ($column == 'manual' || $column == 'sequence'):
				return static::MANUAL;

			case ($column == 'date' || $column == 'visible_from'):
				return static::DATE;

			default:
				return static::ALPHABETIC;
		}
	}

	public function directionToInt($direction)
	{
		return ($direction === 'asc')? static::ASC : static::DESC;
	}

	public function getColumn()
	{
		return $this->_column;
	}

	public function getDirection()
	{
		return $this->_direction;
	}

	protected function _setFromInt($int)
	{
		if ($int & static::ALPHABETIC) {
			$this->_column = 'title';
		} elseif ($int & static::DATE) {
			$this->_column = 'visible_from';
		} else {
			$this->_column = 'sequence';
		}

		$this->_direction = ($int & static::ASC)? 'asc' : 'desc';
	}

	protected function _setFromColumnAndDirection($column, $direction)
	{
		$this->_int = $this->columnToInt($column) | $this->directionToInt($direction);
	}
}