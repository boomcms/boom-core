<?php

namespace Boom\Link;

class External extends \Boom\Link
{
	protected $_link;

	public function __construct($link)
	{
		$this->_link = $link;
	}

	public function url()
	{
		return $this->_link;
	}
}