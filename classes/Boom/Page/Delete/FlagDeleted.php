<?php

namespace Boom\Page\Delete;

class FlagDeleted extends \Boom\Page\Command
{
	public function execute(\Boom\Page $page)
	{
		\DB::update('pages')
			->set(array('deleted' => true))
			->where('id', '=', $page->getId())
			->execute();

		\ORM::factory('Page_MPTT', $page->getId())->delete();
	}
}