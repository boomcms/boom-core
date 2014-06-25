<?php

namespace Boom\Asset;

use \Boom\Asset as Asset;

abstract class Archive
{
	protected $filename;

	abstract public function addAsset(Asset $asset);

	abstract public function close();

	public function delete()
	{
		if (file_exists($this->filename)) {
			unlink($this->filename);
		}
	}

	public function getFilename()
	{
		return $this->filename;
	}
}