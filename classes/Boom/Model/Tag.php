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
	);

	protected $_table_name = 'tags';

	// The value for the 'type' property for asset tags.
	const ASSET = 1;

	// The value for the 'type' property for page tags.
	const PAGE = 2;

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
}