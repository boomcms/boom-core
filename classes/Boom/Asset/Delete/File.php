<?php

namespace Boom\Asset\Delete;

class File extends \Boom\Asset\Command
{
	public function execute(\Boom\Asset $asset)
	{
		@unlink($asset->getFilename());
	}
}