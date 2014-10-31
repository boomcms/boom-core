<?php

namespace Boom\Exception;

class UnsupportedMimeType extends \Exception
{
	private $mimetype;

	public function __construct($mimetype) {
		$this->mimetype = $mimetype;
	}

	public function getMimetype()
	{
		return $this->mimetype;
	}
}