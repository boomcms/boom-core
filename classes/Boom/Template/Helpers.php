<?php

namespace Boom\Template;

class Helpers
{
	/**
	 * Returns an array of the ID and name of all templates which exist in the database.
	 *
	 * This is useful for building <select> boxes of available templates, e.g.:
	 *
	 *	<?= Form::select('template_id', \Boom\Template\Helpers::names()) ?>
	 *
	 *
	 * @return array
	 */
	public static function names()
	{
		return DB::select('id', 'name')
			->from('templates')
			->order_by('name', 'asc')
			->as_array('id', 'name');
	}
}