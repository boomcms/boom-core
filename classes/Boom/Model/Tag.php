<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	BoomCMS
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Model_Tag extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'type'		=>	'',
		'slug_short'	=>	'',
		'slug_long'		=>	'',
	);

	protected $_table_name = 'tags';

	// The value for the 'type' property for asset tags.
	const ASSET = 1;

	// The value for the 'type' property for page tags.
	const PAGE = 2;

	public function check_slugs_are_defined()
	{
		if ( ! $this->slug_short)
		{
			$this->slug_short = $this->create_short_slug($this->name);
		}

		if ( ! $this->slug_long)
		{
			$this->slug_long = $this->create_long_slug($this->name);
		}
	}

	public function create(Validation $validation = NULL)
	{
		$this->check_slugs_are_defined();

		parent::create($validation);
	}

	public function create_long_slug($name)
	{
		$parts = explode('/', $name);

		if (count($parts) === 1)
		{
			return $this->create_short_slug($name);
		}

		foreach ($parts as & $part)
		{
			$part = URL::title($part);
		}

		$slug = $original = implode('/', $parts);
		$i = 0;

		while (ORM::factory('tag', array('slug_long' => $slug))->loaded())
		{
			$i++;
			$slug = "$original$i";
		}

		return $slug;
	}

	public function create_short_slug($name)
	{
		$name = preg_replace('|.*/|', '', $name);

		$slug = $original = URL::title($name);
		$i = 0;

		while (ORM::factory('tag', array('slug_short' => $slug))->loaded())
		{
			$i++;
			$slug = "$original$i";
		}

		return $slug;
	}

	/**
	* Filters for the versioned person columns
	* @link http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'name' => array(
				array('trim'),
			),
	   );
	}

	/**
	 * Returns an associative array of tag IDs and names
	 *
	 * The returned array can be passed to Form::select();
	 *
	 * @param integer $type
	 * @return array
	 */
	public function names($type)
	{
		return DB::select('id', 'name')
			->from('tags')
			->where('type', '=', $type)
			->order_by('name', 'asc')
			->execute($this->_db)
			->as_array('id', 'name');
	}

	/**
	 * ORM Validation rules
	 *
	 * @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	 */
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			),
		);
	}

	public function update(Validation $validation = NULL)
	{
		$this->check_slugs_are_defined();

		parent::update($validation);
	}
}