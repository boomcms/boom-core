<?php

namespace Boom;

abstract class Link
{
	public function __toString()
	{
		return (string) $this->url();
	}

	public static function factory($link)
	{
		return (ctype_digit($link) || substr($link, 0, 1) == '/')? new Link\Internal($link) : new Link\External($link);
	}

	public function isExternal()
	{
		return $this instanceof Link\External;
	}

	public function isInternal()
	{
		return $this instanceof Link\Internal;
	}

	abstract public function url();
}