<?php

namespace Boom\Model;

use \DB as DB;
use \ORM as ORM;

class Template extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
		'filename'		=>	'',
	);

	protected $_table_name = 'templates';

	/**
	 * Returns an array of the ID and name of all templates which exist in the database.
	 *
	 * This is useful for building <select> boxes of available templates, e.g.:
	 *
	 *	<?= Form::select('template_id', ORM::factory('Template')->names()) ?>
	 *
	 *
	 * @return array
	 */
	public function names()
	{
		return DB::select('id', 'name')
			->from('templates')
			->order_by('name', 'asc')
			->execute($this->_db)
			->as_array('id', 'name');
	}

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
			),
			'filename' => array(
				array('not_empty'),
			),
		);
	}
}