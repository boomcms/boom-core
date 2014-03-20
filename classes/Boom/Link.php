<?php

abstract class Boom_Link
{
	public function __toString()
	{
		return (string) $this->url();
	}

	public static function factory($link)
	{
		return (ctype_digit($link) OR substr($link, 0, 1) == '/')? new Link_Internal($link) : new Link_External($link);
	}

	public function is_external()
	{
		return $this instanceof Link_External;
	}

	public function is_internal()
	{
		return $this instanceof Link_Internal;
	}

	abstract public function url();
}