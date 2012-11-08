<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*/
class Sledge_Model_Page_URI extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array('page' => array('model' => 'Page', 'foreign_key' => 'page_id'));
	protected $_table_columns = array(
		'id'			=>	'',
		'page_id'		=>	'',
		'uri'			=>	'',
		'primary_uri'	=>	'',
		'redirect'		=>	'',
	);
	protected $_cache_columns = array('uri');

	/**
	 * Checks that the URI is unique before saving.
	 * This can't be done by a unique index on the table as the uri column is too long to be indexed.
	 */
	public function create(Validation $validation = NULL)
	{
		// Does the URI already exist?
		$exists = DB::select('id')
			->from('page_uris')
			->where('uri', '=', $this->uri)
			->limit(1)
			->execute();

		if ($exists->count() > 0)
		{
			throw new Exception("URI :uri is already in use", array(':uri' => $this->uri));
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
			'uri' => array(
				array('max_length', array(':value', 2048)),
			),
		);
	}

	public function filters()
	{
		return array(
			'uri' => array(
				array(array($this, 'valid_uri')),
			),
		);
	}

	/**
	* Make a URI valid.
	* Remove extra /'s etc.
	*/
	protected function valid_uri($uri)
	{
		// Make sure it's a uri and not a URL.
		$uri = parse_url($uri, PHP_URL_PATH);

		// Remove a leading '/'
		if (substr($uri, 0, 1) == '/')
		{
			$uri = substr($uri, 1);
		}

		// Remove a trailing '/'
		if (substr($uri, -1, 1) == '/')
		{
			$uri = substr($uri, 0, -1);
		}

		// Remove duplicate forward slashes.
		$uri = preg_replace('|/+|', '/', $uri);

		// Make sure there's no HTML in there.
		$uri = strip_tags($uri);

		return $uri;
	}
}
