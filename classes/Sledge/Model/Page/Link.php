<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*/
class Sledge_Model_Page_Link extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array('page' => array());
	protected $_table_columns = array(
		'id'			=>	'',
		'page_id'		=>	'',
		'location'		=>	'',
		'is_primary'	=>	'',
		'redirect'		=>	'',
	);
	protected $_cache_columns = array('location');

	/**
	 * Checks that the URI is unique before saving.
	 * This can't be done by a unique index on the table as the uri column is too long to be indexed.
	 */
	public function create(Validation $validation = NULL)
	{
		// Does the URI already exist?
		$exists = DB::select('id')
			->from('page_links')
			->where('location', '=', $this->location)
			->limit(1)
			->execute();

		if ($exists->count() > 0)
		{
			throw new Exception("Link :link is already in use", array(':link' => $this->location));
		}

		return parent::create($validation);
	}

	/**
	* ORM Validation rules
	* @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	public function rules()
	{
		return array(
			'page_id' => array(
				array('not_empty'),
				array('numeric'),
			),
			'location' => array(
				array('max_length', array(':value', 2048)),
			),
		);
	}

	public function filters()
	{
		return array(
			'location' => array(
				array('trim'),
				array('strip_tags'),								// Make sure there's no HTML in there.
				array('parse_url', array(':value', PHP_URL_PATH)),		// Remove the hostname
				array('trim', array(':value', '/')),					// Remove '/' from the beginning or end of the link
				array('preg_replace', array('|/+|', '/', ':value')),		// Remove duplicate forward slashes.
			),
		);
	}
}
