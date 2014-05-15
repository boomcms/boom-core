<?php

namespace Boom\Page\Delete;

class FromLinkSets extends \Boom\Page\Command
{
	public function execute(\Boom\Page $page)
	{
		\DB::delete('chunk_linkset_links')
			->where('target_page_id', '=', $page->getId())
			->execute();
	}
}