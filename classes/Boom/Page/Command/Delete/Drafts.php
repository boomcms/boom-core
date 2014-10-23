<?php

namespace Boom\Page\Command\Delete;

use \Boom\Page\Page as Page;
use \DB as DB;

class Drafts extends \Boom\Page\Command
{
	public function execute(Page $page)
	{
		DB::delete('page_versions')
			->where('page_id', '=', $page->getId())
			->and_where_open()
					->where('embargoed_until', '=', null)
					->or_where('embargoed_until', '>', time())
			->and_where_close()
			->where('stashed', '=', false)
			->execute();
	}
}