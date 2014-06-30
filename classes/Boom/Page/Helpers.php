<?php

namespace Boom\Page;

use \DB as DB;

class Helpers
{
	public static function idByInternalName($name)
	{
		$results = DB::select('id')
			->from('pages')
			->where('internal_name', '=', $name)
			->execute()
			->as_array();

		if (isset($results[0])) {
			return $results[0]['id'];
		}
	}
}