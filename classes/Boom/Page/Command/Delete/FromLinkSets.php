<?php

namespace Boom\Page\Command\Delete;

use \DB as DB;

class FromLinkSets extends \Boom\Page\Command
{
	public function execute(\Boom\Page $page)
	{
		DB::delete('chunk_linkset_links')
			->where('target_page_id', '=', $page->getId())
			->execute();
	}
}