<?php

namespace Boom\Page\Command\Delete;

use \DB as DB;
use \ORM as ORM;

class FlagDeleted extends \Boom\Page\Command
{
	public function execute(\Boom\Page $page)
	{
		DB::update('pages')
			->set(array('deleted' => true))
			->where('id', '=', $page->getId())
			->execute();

		ORM::factory('Page_MPTT', $page->getId())->delete();
	}
}