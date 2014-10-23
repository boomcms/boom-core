<?php

namespace Boom\Assets\Delete;

class FromDatabase extends \Boom\Asset\Command
{
	public function execute(\Boom\Asset $asset)
	{
		\DB::delete('assets')
			->where('id', '=', $asset->getId())
			->execute();
	}
}