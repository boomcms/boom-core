<?php

namespace Boom\Model;

use \DB as DB;
use \ORM as ORM;

class Tag extends ORM
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

	public function count_pages()
	{
		$result = DB::select(array(DB::expr('count(*)'), 'c'))
			->from('pages_tags')
			->where('tag_id', '=', $this->id)
			->execute($this->_db)
			->as_array();

		return $result[0]['c'];
	}

	public function create(Validation $validation = null)
	{
		$this->check_slugs_are_defined();

		return parent::create($validation);
	}

	public function create_long_slug($name)
	{
		$parts = explode('/', $name);

		if (count($parts) === 1)
		{
			array_unshift($parts, 'tag');
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

		return \URL::title($name);
	}

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

	public function update(Validation $validation = null)
	{
		$this->check_slugs_are_defined();

		return parent::update($validation);
	}
}