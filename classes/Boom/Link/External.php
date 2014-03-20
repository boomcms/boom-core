<?php

class Boom_Link_External extends Link
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