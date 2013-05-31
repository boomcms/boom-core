<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
* @package	BoomCMS
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Model_Template extends ORM
{
	/**
	 * The name of the directory (in the views directory) where template files are stored.
	 */
	const DIRECTORY = 'site/templates/';

	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
		'filename'		=>	'',
	);

	protected $_table_name = 'templates';

	public function controller()
	{
		$parts = explode('_', $this->filename);

		foreach ($parts as & $part)
		{
			$part = ucfirst($part);
		}

		return implode('_', $parts);
	}

	/**
	 * Determines whether the template file exists.
	 *
	 * @uses	Kohana::find_file()
	 * @return boolean
	 */
	public function file_exists()
	{
		return (bool) Kohana::find_file("views", $this->path());
	}

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

	/**
	 * Returns a count of the number of the pages which use the current template.
	 *
	 * @return int
	 */
	public function page_count()
	{
		if ( ! $this->loaded())
		{
			return 0;
		}

		// Query the database for the number of pages using this template and return the result.
		return ORM::factory('Page_Version')
			->join(array(
				DB::select(array(DB::expr('max(id)'), 'id'))
					->from('page_versions')
					->group_by('page_id'),
				'current_version'
			))
			->on('page_version.id', '=', 'current_version.id')
			->where('template_id', '=', $this->id)
			->where('page_deleted', '=', FALSE)
			->count_all();
	}

	public function path()
	{
		return static::DIRECTORY.$this->filename;
	}

	/**
	 * ORM Validation rules
	 * @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	 */
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