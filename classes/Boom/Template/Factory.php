<?php

namespace Boom\Template;

abstract class Factory
{
	public static function byFilename($filename)
	{
		return new Template(new \Model_Template(array('filename' => $filename)));
	}
}