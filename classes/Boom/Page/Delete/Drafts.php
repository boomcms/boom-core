<?php

namespace \Boom\Page\Delete;

class Drafts extends \Boom\Page\Command
{
	public function execute(\Boom\Page $page)
	{
		DB::delete('page_versions')
			->where('page_id', '=', $page->getId())
			->and_where_open()
					->where('embargoed_until', '=', null)
					->or_where('embargoed_until', '>', time())
			->and_where_close()
			->where('stashed', '=', false);
	}
}